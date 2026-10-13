<?php

namespace App\Http\Controllers;

use App\Models\Technician;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TechnicianController extends Controller
{
    public function index()
    {
        $technicians = Technician::with(['assignments.ticket'])->get()->map(function ($t) {
            $activeTickets = $t->assignments
                ->where('is_active', true)
                ->map(function ($a) {
                    return [
                        'id'       => $a->ticket?->ticket_number ?? 'TK-' . $a->ticket_id,
                        'title'    => $a->ticket?->title ?? 'Tiket Perbaikan',
                        'priority' => $a->ticket?->priority ?? 'Normal',
                    ];
                })->values()->toArray();

            return [
                'id'         => $t->id,
                'name'       => $t->name,
                'nik'        => $t->nik,
                'area'       => $t->area ?? 'Situbondo',
                'status'     => $t->status ?? 'Available',
                'tickets'    => count($activeTickets),
                'role'       => $t->position ?? 'Teknisi',
                'phone'      => $t->phone ?? '-',
                'email'      => $t->email ?? '-',
                'joined_iso' => $t->joined_at ? $t->joined_at->format('Y-m-d') : '2024-01-01',
                'exp_years'  => $t->exp_years ?? 1,
                'rating'     => (float) $t->rating,
                'active'     => $activeTickets,
            ];
        })->toArray();

        $assignableTickets = Ticket::whereIn('status', ['Open', 'Assigned', 'In Progress'])
            ->get()
            ->map(function ($tk) {
                return [
                    'id'       => $tk->ticket_number,
                    'title'    => $tk->title,
                    'priority' => $tk->priority,
                    'customer' => $tk->customer_name ?? $tk->customer?->name ?? 'Customer',
                    'area'     => $tk->location ?? 'Situbondo',
                ];
            })->toArray();

        return view('technicians', [
            'technicians'       => $technicians,
            'assignableTickets' => $assignableTickets,
        ]);
    }
}

