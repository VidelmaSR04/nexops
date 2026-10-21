<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Technician;
use App\Models\Ticket;
use App\Models\TicketAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AssignmentController extends Controller
{
    public function store(Request $request, string $ticketId): JsonResponse
    {
        $validated = $request->validate([
            'technician_id' => [
                'required',
                'integer',
                Rule::exists('technicians', 'id')->whereNull('deleted_at'),
            ],
        ]);

        [$assignment, $created] = DB::transaction(function () use ($ticketId, $validated) {
            $ticket = Ticket::with('customer')
                ->where(fn ($query) => $query->where('id', $ticketId)->orWhere('ticket_number', $ticketId))
                ->lockForUpdate()
                ->firstOrFail();

            if (! $ticket->customer instanceof Customer) {
                throw ValidationException::withMessages([
                    'customer_id' => 'Tiket harus terhubung dengan pelanggan aktif sebelum ditugaskan.',
                ]);
            }

            if (! in_array($ticket->status, ['Open', 'Assigned', 'In Progress'], true)) {
                throw ValidationException::withMessages([
                    'ticket_id' => 'Tiket dengan status ini tidak dapat ditugaskan.',
                ]);
            }

            $technician = Technician::lockForUpdate()->findOrFail($validated['technician_id']);
            if (! in_array($technician->status, ['Available', 'On Task'], true)) {
                throw ValidationException::withMessages([
                    'technician_id' => 'Teknisi tidak tersedia untuk menerima penugasan.',
                ]);
            }

            $activeAssignments = $ticket->assignments()
                ->where('is_active', true)
                ->lockForUpdate()
                ->get();

            $existingForTechnician = $activeAssignments
                ->firstWhere('technician_id', $technician->id);
            if ($existingForTechnician) {
                return [$existingForTechnician->load(['ticket.customer', 'technician']), false];
            }

            foreach ($activeAssignments as $oldAssignment) {
                $oldAssignment->update([
                    'is_active' => false,
                    'unassigned_at' => now(),
                ]);

                $this->refreshTechnicianStatus(
                    Technician::lockForUpdate()->find($oldAssignment->technician_id)
                );
            }

            $assignment = TicketAssignment::create([
                'ticket_id' => $ticket->id,
                'technician_id' => $technician->id,
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
                'is_active' => true,
            ]);

            $technician->update(['status' => 'On Task']);
            $ticket->update([
                'assigned_tech_name' => $technician->name,
                'status' => $ticket->status === 'Open' ? 'Assigned' : $ticket->status,
            ]);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'user_name' => Auth::user()?->name,
                'subject_type' => Ticket::class,
                'subject_id' => $ticket->id,
                'action' => 'assigned',
                'description' => "Tiket #{$ticket->ticket_number} ditugaskan kepada {$technician->name}.",
            ]);

            return [$assignment->load(['ticket.customer', 'technician']), true];
        });

        return response()->json([
            'success' => true,
            'message' => 'Tiket berhasil ditugaskan.',
            'data' => $assignment,
        ], $created ? 201 : 200);
    }

    public function destroy(string $ticketId, int $assignmentId): JsonResponse
    {
        $assignment = DB::transaction(function () use ($ticketId, $assignmentId) {
            $ticket = Ticket::where(fn ($query) => $query->where('id', $ticketId)->orWhere('ticket_number', $ticketId))
                ->lockForUpdate()
                ->firstOrFail();

            $assignment = TicketAssignment::where('ticket_id', $ticket->id)
                ->where('id', $assignmentId)
                ->where('is_active', true)
                ->lockForUpdate()
                ->firstOrFail();

            $assignment->update([
                'is_active' => false,
                'unassigned_at' => now(),
            ]);

            $technician = Technician::lockForUpdate()->find($assignment->technician_id);
            $this->refreshTechnicianStatus($technician);

            $ticket->refresh();
            $ticket->update([
                'assigned_tech_name' => $ticket->assigned_tech_name === $technician?->name
                    ? null
                    : $ticket->assigned_tech_name,
                'status' => $ticket->status === 'Assigned' ? 'Open' : $ticket->status,
            ]);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'user_name' => Auth::user()?->name,
                'subject_type' => Ticket::class,
                'subject_id' => $ticket->id,
                'action' => 'unassigned',
                'description' => "Penugasan tiket #{$ticket->ticket_number} dibatalkan.",
            ]);

            return $assignment->load(['ticket.customer', 'technician']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Penugasan tiket berhasil dibatalkan.',
            'data' => $assignment,
        ]);
    }

    private function refreshTechnicianStatus(?Technician $technician): void
    {
        if (! $technician || $technician->activeAssignments()->exists()) {
            return;
        }

        if ($technician->status === 'On Task') {
            $technician->update(['status' => 'Available']);
        }
    }
}
