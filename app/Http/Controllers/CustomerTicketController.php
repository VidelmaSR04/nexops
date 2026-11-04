<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Technician;
use App\Models\Ticket;
use App\Models\TicketAssignment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CustomerTicketController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $user = $this->customerUser($request);
        $customer = $user->customer;
        $tickets = $customer
            ? $customer->tickets()->with('activeTechnicians')->latest()->get()
            : collect();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'customer' => $customer,
                    'tickets' => $tickets,
                ],
            ]);
        }

        return view('customer.tickets', [
            'customer' => $customer,
            'tickets' => $tickets,
            'issueCategories' => Ticket::ISSUE_CATEGORIES,
        ]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $user     = $this->customerUser($request);
        $customer = $user->customer;

        if (! $customer) {
            return response()->json(['success' => false, 'message' => 'Akun tidak terhubung dengan OPD.'], 403);
        }

        $ticket = $customer->tickets()
            ->with(['activeTechnicians', 'materials', 'attachments'])
            ->where(fn ($q) => $q->where('id', $id)->orWhere('ticket_number', $id))
            ->first();

        if (! $ticket) {
            return response()->json(['success' => false, 'message' => 'Tiket tidak ditemukan atau bukan milik OPD Anda.'], 404);
        }

        return response()->json(['success' => true, 'data' => $ticket]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $user = $this->customerUser($request);
        $customer = $user->customer;

        if (! $customer || $customer->type !== 'opd' || $customer->status !== 'Aktif') {
            throw ValidationException::withMessages([
                'customer' => 'Akun harus terhubung dengan OPD aktif sebelum dapat membuat tiket.',
            ]);
        }

        $validated = $request->validate([
            'reporter_name' => ['required', 'string', 'max:150'],
            'reporter_contact' => ['required', 'string', 'regex:/^[0-9]{1,14}$/'],
            'issue_category' => ['required', Rule::in(Ticket::ISSUE_CATEGORIES)],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:10000'],
            'location' => ['required', 'string', 'max:100'],
            'priority' => ['required', Rule::in(['Low', 'Normal', 'High', 'Critical'])],
        ]);

        $ticket = DB::transaction(function () use ($customer, $user, $validated): Ticket {
            $ticketNumber = $this->nextTicketNumber();
            $slaHours = match ($validated['priority']) {
                'Critical' => 2,
                'High' => 4,
                'Normal' => 8,
                'Low' => 24,
            };
            $technician = $this->findAvailableTechnicianFor($customer);

            $ticket = Ticket::create([
                'ticket_number' => $ticketNumber,
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'created_by' => $user->id,
                'reporter_name' => $validated['reporter_name'],
                'reporter_contact' => $validated['reporter_contact'],
                'title' => $validated['title'],
                'subtitle' => $validated['title'],
                'issue_category' => $validated['issue_category'],
                'description' => $validated['description'],
                'location' => $validated['location'],
                'priority' => $validated['priority'],
                'status' => $technician ? 'Assigned' : 'Open',
                'assigned_tech_name' => $technician?->name,
                'sla_due_at' => now()->addHours($slaHours),
            ]);

            if ($technician) {
                TicketAssignment::create([
                    'ticket_id' => $ticket->id,
                    'technician_id' => $technician->id,
                    'assigned_by' => null,
                    'assigned_at' => now(),
                    'is_active' => true,
                ]);
                $technician->update(['status' => 'On Task']);
            }

            ActivityLog::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'subject_type' => Ticket::class,
                'subject_id' => $ticket->id,
                'action' => $technician ? 'created_and_assigned' : 'created',
                'description' => $technician
                    ? "Tiket #{$ticketNumber} dibuat oleh {$user->name} dan ditugaskan kepada {$technician->name}."
                    : "Tiket #{$ticketNumber} dibuat oleh {$user->name}; belum ada teknisi tersedia di area OPD.",
            ]);

            return $ticket->load(['customer', 'activeTechnicians']);
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tiket berhasil dikirim.',
                'data' => $ticket,
            ], 201);
        }

        return redirect()
            ->route('customer.tickets.index')
            ->with('success', "Tiket {$ticket->ticket_number} berhasil dikirim.");
    }

    private function customerUser(Request $request): User
    {
        $user = $request->user();
        abort_unless($user instanceof User && $user->role === 'customer', 403);

        return $user;
    }

    private function nextTicketNumber(): string
    {
        $lastTicketNumber = Ticket::withTrashed()
            ->lockForUpdate()
            ->orderByDesc('id')
            ->value('ticket_number');
        $nextNumber = 1001;

        if (is_string($lastTicketNumber) && preg_match('/TK-(\d+)/', $lastTicketNumber, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        do {
            $ticketNumber = 'TK-'.$nextNumber;
            $nextNumber++;
        } while (Ticket::withTrashed()->where('ticket_number', $ticketNumber)->exists());

        return $ticketNumber;
    }

    private function findAvailableTechnicianFor(Customer $customer): ?Technician
    {
        $customerArea = $this->normalizeArea($customer->area);
        if ($customerArea === '') {
            return null;
        }

        return Technician::query()
            ->where('status', 'Available')
            ->whereNotNull('area')
            ->withCount('activeAssignments')
            ->orderBy('active_assignments_count')
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->first(fn (Technician $technician): bool => $this->normalizeArea($technician->area) === $customerArea);
    }

    private function normalizeArea(?string $area): string
    {
        $area = preg_replace('/^(kecamatan|kec\.?|kelurahan|kel\.?|area)\s*/iu', '', trim((string) $area));

        return mb_strtolower(preg_replace('/\s+/u', ' ', trim((string) $area)) ?? '');
    }
}
