<?php

namespace App\Http\Controllers;

use App\Models\Technician;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\TechnicianRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TechnicianController extends Controller
{
    public function index(Request $request)
    {
        $query = Technician::with([
            'assignments' => fn ($query) => $query->with('ticket.customer')->orderByDesc('assigned_at'),
        ]);

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function (Builder $technicians) use ($search) {
                $technicians->where('name', 'like', '%' . $search . '%')
                    ->orWhere('nik', 'like', '%' . $search . '%')
                    ->orWhere('area', 'like', '%' . $search . '%');
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }
        if ($request->filled('area')) {
            $query->where('area', $request->string('area')->toString());
        }

        $pagination = null;
        if ($request->filled('per_page')) {
            $validated = $request->validate(['per_page' => 'integer|min:1|max:100']);
            $page = $query->orderByDesc('id')->paginate($validated['per_page']);
            $technicians = collect($page->items())
                ->map(fn (Technician $technician) => $this->serializeTechnician($technician))
                ->all();
            $pagination = [
                'current_page' => $page->currentPage(),
                'last_page' => $page->lastPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
            ];
        } else {
            $technicians = $query->orderByDesc('id')->get()
                ->map(fn (Technician $technician) => $this->serializeTechnician($technician))
                ->all();
        }

        $assignableTickets = Ticket::whereIn('status', ['Open', 'Assigned', 'In Progress'])
            ->with('customer')
            ->whereHas('customer')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($tk) {
                return [
                    'id'       => $tk->ticket_number,
                    'title'    => $tk->title,
                    'priority' => $tk->priority,
                    'customer' => $tk->customer?->name ?? $tk->customer_name ?? '',
                    'location' => $tk->location ?? $tk->customer?->area ?? '',
                ];
            })->toArray();

        if ($request->is('api/*') || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data teknisi berhasil diambil.',
                'data' => [
                    'technicians' => $technicians,
                    'assignableTickets' => $assignableTickets,
                    'pagination' => $pagination,
                ],
            ]);
        }

        return view('technicians', [
            'technicians'       => $technicians,
            'assignableTickets' => $assignableTickets,
        ]);
    }

    public function show($technicianId): JsonResponse
    {
        $technician = Technician::with([
            'assignments' => fn ($query) => $query->with('ticket.customer')->orderByDesc('assigned_at'),
        ])->findOrFail($technicianId);

        return response()->json([
            'success' => true,
            'message' => 'Detail teknisi berhasil diambil.',
            'data' => $this->serializeTechnician($technician),
        ]);
    }

    public function store(TechnicianRequest $request): JsonResponse
    {
        $validated = $request->validated();
        if ($validated['status'] === 'On Task') {
            throw ValidationException::withMessages([
                'status' => 'Status On Task hanya dapat ditetapkan melalui penugasan tiket.',
            ]);
        }

        $technician = DB::transaction(function () use ($validated): Technician {
            $validated['nik'] = 'TMP-'.Str::random(24);
            $validated['position'] = 'Teknisi';
            $validated['area'] = 'Dinas Komunikasi dan Informatika';
            $validated['latitude'] = null;
            $validated['longitude'] = null;

            $technician = Technician::create($validated);
            $number = $technician->id;
            do {
                $code = 'TK'.str_pad((string) $number, 3, '0', STR_PAD_LEFT);
                $number++;
            } while (Technician::withTrashed()->where('nik', $code)->exists());

            $technician->update(['nik' => $code]);

            return $technician;
        });

        return response()->json([
            'success' => true,
            'message' => 'Teknisi berhasil ditambahkan.',
            'data' => $this->serializeTechnician($technician->load('assignments.ticket')),
        ], 201);
    }

    public function update(TechnicianRequest $request, $technicianId): JsonResponse
    {
        $technician = Technician::findOrFail($technicianId);

        $validated = $request->validated();

        $validated['nik'] = $technician->nik;
        $validated['position'] = 'Teknisi';
        $validated['area'] = 'Dinas Komunikasi dan Informatika';
        unset($validated['latitude'], $validated['longitude']);
        $technician->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Teknisi berhasil diperbarui.',
            'data' => $this->serializeTechnician($technician->fresh()->load('assignments.ticket')),
        ]);
    }

    public function updateStatus(Request $request, $technicianId): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Available,On Task,Offline,Cuti',
        ]);
        $technician = Technician::findOrFail($technicianId);
        $technician->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Status teknisi berhasil diperbarui.',
            'data' => $this->serializeTechnician($technician->fresh()->load('assignments.ticket')),
        ]);
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('technicians', 'id')->whereNull('deleted_at'),
            ],
        ]);

        $technicians = Technician::query()->whereIn('id', $validated['ids'])->get();
        $busyTechnician = $technicians->first(
            fn (Technician $technician): bool => $technician->activeAssignments()->exists(),
        );
        if ($busyTechnician) {
            throw ValidationException::withMessages([
                'ids' => $busyTechnician->name.' masih menangani tiket aktif. Selesaikan atau batalkan penugasannya terlebih dahulu.',
            ]);
        }

        $deletedCount = Technician::query()->whereIn('id', $validated['ids'])->delete();

        return response()->json([
            'success' => true,
            'message' => $deletedCount.' teknisi berhasil dihapus.',
            'deleted_count' => $deletedCount,
        ]);
    }

    public function destroy($technicianId): JsonResponse
    {
        $technician = Technician::findOrFail($technicianId);
        $this->assertStatusMatchesAssignments($technician, 'Offline');
        $technician->update(['status' => 'Offline']);

        return response()->json([
            'success' => true,
            'message' => 'Teknisi berhasil dinonaktifkan.',
            'data' => $this->serializeTechnician($technician->fresh()->load('assignments.ticket')),
        ]);
    }

    private function serializeTechnician(Technician $technician): array
    {
        $assignments = $technician->assignments;
        $activeAssignments = $assignments->where('is_active', true);
        $hasStoredCoordinates = $technician->latitude !== null && $technician->longitude !== null;
        $coordinates = $hasStoredCoordinates
            ? ['latitude' => (float) $technician->latitude, 'longitude' => (float) $technician->longitude]
            : $this->simulatedCoordinates($technician);

        $active = $activeAssignments->map(fn ($assignment) => [
            'id' => $assignment->ticket?->ticket_number ?? 'TK-' . $assignment->ticket_id,
            'title' => $assignment->ticket?->title ?? '',
            'priority' => $assignment->ticket?->priority ?? '',
            'opd' => $assignment->ticket?->customer?->name ?? $assignment->ticket?->customer_name ?? '-',
            'address' => $assignment->ticket?->location ?? $assignment->ticket?->customer?->area ?? '-',
            'date' => $assignment->assigned_at?->format('d M Y') ?? '-',
            'work_status' => 'Ditugaskan',
        ])->values()->all();

        $history = $assignments->map(fn ($assignment) => [
            'title' => $assignment->is_active ? 'Ticket ditugaskan' : 'Penugasan ticket berakhir',
            'desc' => ($assignment->ticket?->ticket_number ?? 'TK-' . $assignment->ticket_id)
                . ($assignment->ticket?->title ? ' — ' . $assignment->ticket->title : ''),
            'time' => $assignment->assigned_at?->format('d M Y H:i') ?? '-',
        ])->values()->all();

        return [
            'id' => $technician->id,
            'name' => $technician->name,
            'nik' => $technician->nik,
            'area' => 'Dinas Komunikasi dan Informatika',
            'latitude' => $coordinates['latitude'] ?? null,
            'longitude' => $coordinates['longitude'] ?? null,
            'location_is_simulated' => ! $hasStoredCoordinates && $coordinates !== null,
            'status' => $technician->status,
            'tickets' => $activeAssignments->count(),
            'role' => $technician->position,
            'phone' => $technician->phone ?? '',
            'email' => $technician->email ?? '',
            'joined_iso' => $technician->joined_at?->format('Y-m-d'),
            'rating' => (float) $technician->rating,
            'active' => $active,
            'history' => $history,
            'docs' => [],
        ];
    }

    private function simulatedCoordinates(Technician $technician): ?array
    {
        $area = mb_strtolower(trim((string) $technician->area));
        $center = match ($area) {
            'situbondo' => [-7.7066, 113.9298],
            'besuki' => [-7.7335, 113.6965],
            'lapangan', 'area lapangan' => [-7.7066, 113.9298],
            default => null,
        };

        if ($center === null) {
            return null;
        }

        $seed = hexdec(substr(hash('sha256', $technician->id . ':' . $area), 0, 8)) / 0xffffffff;
        $latitudeOffset = ($seed * 2 - 1) * 0.008;
        $longitudeSeed = fmod($seed * 1.61803398875, 1);
        $longitudeOffset = ($longitudeSeed * 2 - 1) * 0.01;

        return [
            'latitude' => round($center[0] + $latitudeOffset, 7),
            'longitude' => round($center[1] + $longitudeOffset, 7),
        ];
    }

    private function assertStatusMatchesAssignments(Technician $technician, string $status): void
    {
        $hasActiveAssignments = $technician->activeAssignments()->exists();
        $isConsistent = $hasActiveAssignments
            ? $status === 'On Task'
            : $status !== 'On Task';

        if (! $isConsistent) {
            throw ValidationException::withMessages([
                'status' => $hasActiveAssignments
                    ? 'Teknisi masih memiliki tiket aktif dan harus berstatus On Task.'
                    : 'Status On Task hanya dapat ditetapkan melalui penugasan tiket.',
            ]);
        }
    }
}
