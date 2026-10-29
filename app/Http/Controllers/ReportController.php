<?php

namespace App\Http\Controllers;

use App\Models\Technician;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports', [
            'reportTickets' => $this->reportTickets(),
            'reportDataUrl' => route('reports.data'),
        ]);
    }

    public function data(): JsonResponse
    {
        return response()->json([
            'data' => $this->reportTickets(),
            'updated_at' => now()->toIso8601String(),
        ]);
    }

    private function reportTickets(): array
    {
        $tickets = Ticket::query()
            ->with(['customer', 'asset.category', 'technicians'])
            ->orderBy('created_at')
            ->get();
        $legacyTechnicians = Technician::query()
            ->whereIn('name', $tickets->pluck('assigned_tech_name')->filter()->unique())
            ->get()
            ->keyBy('name');

        return $tickets->map(function (Ticket $ticket) use ($legacyTechnicians): array {
            $technician = $ticket->technicians->first()
                ?? $legacyTechnicians->get($ticket->assigned_tech_name);
            $resolvedAt = $ticket->resolved_at
                ?? $ticket->closed_at
                ?? (in_array($ticket->status, ['Resolved', 'Closed'], true) ? $ticket->updated_at : null);

            return [
                'id' => $ticket->ticket_number,
                'created_at' => $ticket->created_at?->toDateString(),
                'resolved_at' => $resolvedAt?->toDateString(),
                'due_at' => $ticket->sla_due_at?->toDateString(),
                'category' => $ticket->asset?->category?->name ?: 'Prioritas '.$ticket->priority,
                'opd' => $ticket->customer?->name ?: ($ticket->customer_name ?: ($ticket->location ?: 'Tidak diketahui')),
                'technician_code' => $technician?->nik,
                'technician_name' => $technician?->name ?: $ticket->assigned_tech_name,
                'rating' => $technician?->rating === null ? null : (float) $technician->rating,
                'status' => $ticket->status,
            ];
        })->all();
    }
}
