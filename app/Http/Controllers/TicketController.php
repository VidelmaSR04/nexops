<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Asset;
use App\Models\Customer;
use App\Models\Technician;
use App\Models\Ticket;
use App\Models\TicketAssignment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    /**
     * Tampilan Halaman Utama Ticket (Blade)
     */
    public function index()
    {
        try {
            Log::info('TicketController@index started');

            $tickets = Ticket::with(['customer', 'asset', 'creator', 'materials', 'attachments', 'activeTechnicians'])
                ->latest()
                ->get();
            $activitiesByTicket = ActivityLog::where('subject_type', Ticket::class)
                ->whereIn('subject_id', $tickets->pluck('id'))
                ->latest()
                ->get()
                ->groupBy('subject_id');

            $rawTickets = $tickets->map(function ($tk) use ($activitiesByTicket) {
                $assignedTech = $tk->activeTechnicians->first()?->name ?? ($tk->assigned_tech_name ?? '-');

                $materials = $tk->materials->map(function ($m) {
                    return [
                        'name' => $m->item_name,
                        'qty'  => $m->quantity,
                        'unit' => $m->unit,
                    ];
                })->toArray();

                $attachments = $tk->attachments->map(function ($a) {
                    return [
                        'name' => $a->file_name,
                        'size' => $a->file_size_human ?? '-',
                    ];
                })->toArray();

                $slaLeft = 'Selesai';
                if (!in_array($tk->status, ['Resolved', 'Closed'])) {
                    if ($tk->sla_due_at && $tk->sla_due_at->isPast()) {
                        $slaLeft = 'SLA Overdue';
                    } elseif ($tk->sla_due_at) {
                        $slaLeft = $tk->sla_due_at->diffForHumans(['parts' => 1]);
                    } else {
                        $slaLeft = 'Sesuai Jadwal';
                    }
                }

                return [
                    'db_id'       => $tk->id,
                    'customer_id' => $tk->customer_id,
                    'id'          => '#' . $tk->ticket_number,
                    'customer'    => $tk->customer?->name ?? $tk->customer_name ?? '-',
                    'location'    => $tk->location ?? ($tk->customer?->area ?? '-'),
                    'reporter_name'    => $tk->reporter_name ?? $tk->customer?->reporter_name ?? '-',
                    'reporter_contact' => $tk->reporter_contact ?? $tk->customer?->reporter_contact ?? '-',
                    'title'       => $tk->title,
                    'category'    => $tk->issue_category ?? '-',
                    'subtitle'    => $tk->subtitle ?? $tk->title,
                    'priority'    => $tk->priority,
                    'status'      => $tk->status,
                    'tech'        => $assignedTech,
                    'created_at'  => $tk->created_at ? $tk->created_at->format('d M Y H:i') : '-',
                    'description' => $tk->description ?? '-',
                    'asset'       => $tk->asset?->name ?? '-',
                    'serial'      => $tk->asset?->serial_number ?? '-',
                    'creator'     => $tk->creator?->name ?? '-',
                    'sla_due'     => $tk->sla_due_at ? $tk->sla_due_at->format('d M Y H:i') : '-',
                    'sla_left'    => $slaLeft,
                    'materials'   => $materials,
                    'attachments' => $attachments,
                    'activities'  => ($activitiesByTicket->get($tk->id) ?? collect())->map(fn ($activity) => [
                        'description' => $activity->description,
                        'user' => $activity->user_name,
                        'created_at' => $activity->created_at?->format('d M Y H:i') ?? '-',
                    ])->values()->all(),
                ];
            })->toArray();

            $summary = [
                'open'        => Ticket::where('status', 'Open')->count(),
                'in_progress' => Ticket::where('status', 'In Progress')->count(),
                'overdue'     => Ticket::whereNotIn('status', ['Resolved', 'Closed'])
                                ->where('sla_due_at', '<', Carbon::now())
                                ->count(),
                'resolved'    => Ticket::whereIn('status', ['Resolved', 'Closed'])->count(),
            ];

            $techList = Technician::pluck('name')->toArray();

            if ($tickets->isNotEmpty()) {
                $first = $tickets->first();
                Log::info('First ticket type: '.get_class($first).', id: '.$first->id);
            } else {
                Log::info('Raw tickets collection is empty');
            }
            Log::info('Raw tickets count: '.count($rawTickets));

            return view('tickets', [
                'rawTickets'  => $rawTickets,
                'summary'     => $summary,
                'technicians' => $techList,
                'issueCategories' => Ticket::ISSUE_CATEGORIES,
                'opds'        => Customer::where('type', 'opd')->orderBy('name')->get(),
            ]);
        } catch (\Exception $e) {
            Log::error('TicketController@index exception: '.$e->getMessage());
            Log::error($e->getTraceAsString());
            // Optionally rethrow or return an error view
            throw $e;
        }
    }
    /**
     * Show the form for creating a new ticket.
     */
    public function create()
    {
        $technicians = Technician::pluck('name')->toArray();

        return view('tickets.create', [
            'technicians' => $technicians,
            'issueCategories' => Ticket::ISSUE_CATEGORIES,
            'opds' => Customer::where('type', 'opd')->orderBy('name')->get(),
        ]);
    }

    /**
     * Simpan Tiket Baru (CRUD Store untuk Web & API)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'        => ['required', 'integer', Rule::exists('customers', 'id')->where('type', 'opd')->whereNull('deleted_at')],
            'location'           => 'required|string|max:100',
            'reporter_name'      => 'required|string|max:150',
            'reporter_contact'   => ['nullable', 'string', 'regex:/^[0-9]{1,14}$/'],
            'priority'           => 'required|in:Low,Normal,High,Critical',
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'issue_category'     => ['nullable', Rule::in(Ticket::ISSUE_CATEGORIES)],
            'assigned_tech_name' => 'nullable|string',
            'asset_name'         => 'nullable|string',
        ]);

        $ticketNumber = $this->nextTicketNumber();

        // 2. Hitung SLA otomatis berdasarkan prioritas
        $slaHours = match ($validated['priority']) {
            'Critical' => 2,
            'High'     => 4,
            'Normal'   => 8,
            'Low'      => 24,
            default    => 8,
        };
        $slaDueAt = Carbon::now()->addHours($slaHours);

        // 3. Cari Relasi Customer jika cocok
        $customer = Customer::findOrFail($validated['customer_id']);

        // 4. Cari Relasi Aset jika cocok
        $asset = null;
        if (!empty($validated['asset_name'])) {
            $asset = Asset::where('name', 'like', '%' . $validated['asset_name'] . '%')->first();
        }

        // 5. Tentukan Status Awal
        $assignedTechName = $validated['assigned_tech_name'] ?? null;
        $status = (!empty($assignedTechName) && $assignedTechName !== '-')
            ? 'Assigned'
            : 'Open';

        $ticket = Ticket::create([
            'ticket_number'      => $ticketNumber,
            'customer_id'        => $customer?->id,
            'customer_name'      => $customer->name,
            'asset_id'           => $asset?->id,
            'created_by'         => Auth::id() ?? 1,
            'assigned_tech_name' => $assignedTechName !== '-' ? $assignedTechName : null,
            'title'              => $validated['title'],
            'subtitle'           => $validated['title'],
            'issue_category'     => $validated['issue_category'] ?? null,
            'description'        => $validated['description'] ?? 'Tiket dibuat melalui sistem.',
            'location'           => $validated['location'],
            'reporter_name'      => $validated['reporter_name'],
            'reporter_contact'   => $validated['reporter_contact'] ?? null,
            'priority'           => $validated['priority'],
            'status'             => $status,
            'sla_due_at'         => $slaDueAt,
        ]);

        // 6. Jika Teknisi Ditugaskan, Catat ke TicketAssignment
        if (!empty($assignedTechName) && $assignedTechName !== '-') {
            $technician = Technician::where('name', $assignedTechName)->first();
            if ($technician) {
                TicketAssignment::create([
                    'ticket_id'     => $ticket->id,
                    'technician_id' => $technician->id,
                    'assigned_by'   => Auth::id() ?? 1,
                    'assigned_at'   => Carbon::now(),
                    'is_active'     => true,
                ]);

                // Update status teknisi jadi On Task jika belum
                $technician->update(['status' => 'On Task']);
            }
        }

        // 7. Catat ke Activity Log
        ActivityLog::create([
            'user_id'      => Auth::id() ?? 1,
            'user_name'    => Auth::user()?->name ?? 'Admin',
            'subject_type' => Ticket::class,
            'subject_id'   => $ticket->id,
            'action'       => 'created',
            'description'  => "Tiket #{$ticketNumber} ({$ticket->title}) berhasil dibuat.",
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Tiket #{$ticketNumber} berhasil dibuat.",
                'data'    => $ticket->load(['customer', 'asset', 'activeTechnicians']),
            ], 201);
        }

        return redirect()->route('tickets.index')->with('success', "Tiket #{$ticketNumber} berhasil dibuat.");
    }

    /**
     * Ubah Status Tiket (Open -> In Progress -> Resolved -> Closed)
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:Open,Assigned,In Progress,Waiting,Resolved,Closed',
        ]);

        $ticket = Ticket::where('id', $id)->orWhere('ticket_number', $id)->firstOrFail();
        $oldStatus = $ticket->status;
        $newStatus = $validated['status'];

        $updateData = ['status' => $newStatus];

        if ($newStatus === 'Resolved' && !$ticket->resolved_at) {
            $updateData['resolved_at'] = Carbon::now();
        }
        if ($newStatus === 'Closed' && !$ticket->closed_at) {
            $updateData['closed_at'] = Carbon::now();
        }

        $ticket->update($updateData);

        // Jika Resolved atau Closed, nonaktifkan assignment aktif
        if (in_array($newStatus, ['Resolved', 'Closed'])) {
            $this->deactivateActiveAssignments($ticket);
        }

        // Catat aktivitas
        ActivityLog::create([
            'user_id'      => Auth::id() ?? 1,
            'user_name'    => Auth::user()?->name ?? 'Admin',
            'subject_type' => Ticket::class,
            'subject_id'   => $ticket->id,
            'action'       => 'status_updated',
            'description'  => "Status Tiket #{$ticket->ticket_number} diubah dari {$oldStatus} ke {$newStatus}.",
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Status tiket berhasil diubah menjadi {$newStatus}.",
                'data'    => $ticket,
            ]);
        }

        return redirect()->route('tickets.index')->with('success', "Status tiket berhasil diubah ke {$newStatus}.");
    }

    /**
     * REST API: List Semua Tiket (Untuk Postman)
     */
    public function apiIndex(): JsonResponse
    {
        $tickets = Ticket::with(['customer', 'asset', 'activeTechnicians', 'materials'])->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar tiket operasional berhasil diambil.',
            'data'    => $tickets,
        ]);
    }

    /**
     * REST API: Detail Satu Tiket (Untuk Postman)
     */
    public function apiShow($id): JsonResponse
    {
        $ticket = Ticket::with(['customer', 'asset', 'activeTechnicians', 'materials', 'attachments'])
            ->where('id', $id)
            ->orWhere('ticket_number', $id)
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail tiket berhasil diambil.',
            'data'    => $ticket,
        ]);
    }

    /**
     * Display the specified ticket.
     */
    public function show(Request $request, $id)
    {
        $ticket = Ticket::with(['customer', 'asset', 'creator', 'activeTechnicians', 'materials', 'attachments'])
            ->where('id', $id)
            ->orWhere('ticket_number', $id)
            ->firstOrFail();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Detail tiket berhasil diambil.',
                'data'    => $ticket,
            ]);
        }

        return view('tickets.show', [
            'ticket' => $ticket,
            'technicians' => Technician::query()
                ->whereIn('status', ['Available', 'On Task'])
                ->orderBy('name')
                ->get(),
            'activities' => ActivityLog::where('subject_type', Ticket::class)
                ->where('subject_id', $ticket->id)
                ->latest()
                ->get(),
        ]);
    }

    /**
     * Show the form for editing the specified ticket.
     */
    public function edit(Request $request, $id)
    {
        $ticket = Ticket::with(['customer', 'asset', 'activeTechnicians'])->where('id', $id)->orWhere('ticket_number', $id)->firstOrFail();
        $technicians = Technician::pluck('name')->all();
        $opds = Customer::where('type', 'opd')->orderBy('name')->get();
        $selectedOpdId = $ticket->customer_id ?? $opds->firstWhere('name', $ticket->customer_name)?->id;

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data untuk edit tiket berhasil diambil.',
                'data'    => [
                    'ticket' => $ticket,
                    'technicians' => $technicians,
                    'issueCategories' => Ticket::ISSUE_CATEGORIES,
                    'opds' => $opds,
                    'selectedOpdId' => $selectedOpdId,
                ],
            ]);
        }

        return view('tickets.edit', [
            'ticket' => $ticket,
            'technicians' => $technicians,
            'issueCategories' => Ticket::ISSUE_CATEGORIES,
            'opds' => $opds,
            'selectedOpdId' => $selectedOpdId,
        ]);
    }

    /**
     * Update the specified ticket in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'customer_id'        => ['required', 'integer', Rule::exists('customers', 'id')->where('type', 'opd')->whereNull('deleted_at')],
            'location'           => 'required|string|max:100',
            'reporter_name'      => 'required|string|max:150',
            'reporter_contact'   => ['nullable', 'string', 'regex:/^[0-9]{1,14}$/'],
            'priority'           => 'required|in:Low,Normal,High,Critical',
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'issue_category'     => ['nullable', Rule::in(Ticket::ISSUE_CATEGORIES)],
            'assigned_tech_name' => 'nullable|string',
            'asset_name'         => 'nullable|string',
        ]);

        $ticket = Ticket::where('id', $id)->orWhere('ticket_number', $id)->firstOrFail();

        $assignedTechName = $validated['assigned_tech_name'] ?? null;
        $technician = $assignedTechName && $assignedTechName !== '-'
            ? Technician::where('name', $assignedTechName)->first()
            : null;
        $slaDueAt = $ticket->sla_due_at;
        if ($ticket->priority !== $validated['priority']) {
            $slaHours = match ($validated['priority']) {
                'Critical' => 2,
                'High'     => 4,
                'Normal'   => 8,
                'Low'      => 24,
            };
            $slaDueAt = Carbon::now()->addHours($slaHours);
        }

        // 3. Cari Relasi Customer jika cocok
        $customer = Customer::findOrFail($validated['customer_id']);

        // 4. Cari Relasi Aset jika cocok
        $asset = null;
        if (!empty($validated['asset_name'])) {
            $asset = Asset::where('name', 'like', '%' . $validated['asset_name'] . '%')->first();
        }

        $status = $ticket->status;
        if (in_array($ticket->status, ['Open', 'Assigned'], true)) {
            $status = $technician ? 'Assigned' : 'Open';
        }

        $ticket->update([
            'customer_id'        => $customer?->id,
            'customer_name'      => $customer->name,
            'asset_id'           => $asset?->id,
            'title'              => $validated['title'],
            'subtitle'           => $validated['title'],
            'issue_category'     => $validated['issue_category'] ?? null,
            'description'        => $validated['description'] ?? 'Tiket diperbarui melalui sistem.',
            'location'           => $validated['location'],
            'reporter_name'      => $validated['reporter_name'],
            'reporter_contact'   => $validated['reporter_contact'] ?? null,
            'priority'           => $validated['priority'],
            'status'             => $status,
            'sla_due_at'         => $slaDueAt,
            'assigned_tech_name' => $technician?->name,
        ]);

        $activeAssignments = $ticket->assignments()->where('is_active', true);
        if (!$technician || $activeAssignments->where('technician_id', '!=', $technician->id)->exists()) {
            $this->deactivateActiveAssignments($ticket);
        }
        if ($technician && !$ticket->assignments()
            ->where('technician_id', $technician->id)
            ->where('is_active', true)
            ->exists()) {
            TicketAssignment::create([
                'ticket_id'     => $ticket->id,
                'technician_id' => $technician->id,
                'assigned_by'   => Auth::id(),
                'assigned_at'   => Carbon::now(),
                'is_active'     => true,
            ]);
            $technician->update(['status' => 'On Task']);
        }

        // Catat ke Activity Log
        ActivityLog::create([
            'user_id'      => Auth::id() ?? 1,
            'user_name'    => Auth::user()?->name ?? 'Admin',
            'subject_type' => Ticket::class,
            'subject_id'   => $ticket->id,
            'action'       => 'updated',
            'description'  => "Tiket #{$ticket->ticket_number} ({$ticket->title}) berhasil diperbarui.",
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Tiket #{$ticket->ticket_number} berhasil diperbarui.",
                'data'    => $ticket->load(['customer', 'asset', 'activeTechnicians']),
            ]);
        }

        return redirect()->route('tickets.index')->with('success', "Tiket #{$ticket->ticket_number} berhasil diperbarui.");
    }

    /**
     * Remove the specified ticket from storage.
     */
    public function destroy(Request $request, $id)
    {
        $ticket = Ticket::where('id', $id)->orWhere('ticket_number', $id)->firstOrFail();

        $this->deactivateActiveAssignments($ticket);

        // Catat ke Activity Log sebelum hapus
        ActivityLog::create([
            'user_id'      => Auth::id() ?? 1,
            'user_name'    => Auth::user()?->name ?? 'Admin',
            'subject_type' => Ticket::class,
            'subject_id'   => $ticket->id,
            'action'       => 'deleted',
            'description'  => "Tiket #{$ticket->ticket_number} ({$ticket->title}) berhasil dihapus.",
        ]);

        $ticket->delete(); // Soft delete due to SoftDeletes trait

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Tiket #{$ticket->ticket_number} berhasil dihapus.",
            ]);
        }

        return redirect()->route('tickets.index')->with('success', "Tiket #{$ticket->ticket_number} berhasil dihapus.");
    }

    private function deactivateActiveAssignments(Ticket $ticket): void
    {
        $assignments = $ticket->assignments()
            ->where('is_active', true)
            ->with('technician')
            ->get();

        if ($assignments->isEmpty()) {
            return;
        }

        $ticket->assignments()
            ->whereIn('id', $assignments->pluck('id'))
            ->update([
                'is_active' => false,
                'unassigned_at' => Carbon::now(),
            ]);

        $assignments->pluck('technician')
            ->filter()
            ->unique('id')
            ->each(function (Technician $technician): void {
                if ($technician->status === 'On Task' && ! $technician->activeAssignments()->exists()) {
                    $technician->update(['status' => 'Available']);
                }
            });
    }

    private function nextTicketNumber(): string
    {
        $lastTicketNumber = Ticket::withTrashed()
            ->orderByDesc('id')
            ->value('ticket_number');
        $nextNumber = 1001;

        if (is_string($lastTicketNumber) && preg_match('/^TK-(\d+)$/', $lastTicketNumber, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        do {
            $ticketNumber = 'TK-'.$nextNumber;
            $nextNumber++;
        } while (Ticket::withTrashed()->where('ticket_number', $ticketNumber)->exists());

        return $ticketNumber;
    }
}
