<?php

namespace Database\Seeders;

use App\Models\Technician;
use App\Models\Ticket;
use App\Models\TicketAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoTicketSeeder extends Seeder
{
    public function run(): void
    {
        $customerUser = User::query()
            ->where('role', 'customer')
            ->whereNotNull('customer_id')
            ->whereHas('customer', fn ($query) => $query
                ->where('type', 'opd')
                ->where('status', 'Aktif'))
            ->with('customer')
            ->orderBy('id')
            ->first();

        if (! $customerUser?->customer) {
            throw new \RuntimeException('Hubungkan akun Customer ke OPD aktif sebelum menjalankan DemoTicketSeeder.');
        }

        $customer = $customerUser->customer;
        $technicians = Technician::query()
            ->where('status', 'Available')
            ->whereDoesntHave('activeAssignments')
            ->orderBy('id')
            ->limit(2)
            ->get();

        $demoTicketNumbers = ['TK-DEMO-0002', 'TK-DEMO-0003'];
        $assignedTechnicians = [];

        foreach ($demoTicketNumbers as $ticketNumber) {
            $existingTechnician = Ticket::query()
                ->where('ticket_number', $ticketNumber)
                ->first()
                ?->activeTechnicians()
                ->first();

            if ($existingTechnician) {
                $assignedTechnicians[] = $existingTechnician;

                continue;
            }

            $technician = $technicians->shift();
            if (! $technician) {
                throw new \RuntimeException('DemoTicketSeeder membutuhkan 2 teknisi tersedia tanpa penugasan aktif.');
            }

            $assignedTechnicians[] = $technician;
        }

        $adminId = User::query()->where('role', 'admin')->value('id');
        $now = now();
        $demoTickets = [
            [
                'ticket_number' => 'TK-DEMO-0001',
                'title' => '[DEMO] Koneksi internet terputus',
                'category' => 'Koneksi Putus',
                'priority' => 'High',
                'status' => 'Open',
                'sla_due_at' => $now->copy()->subHour(),
                'resolved_at' => null,
                'technician' => null,
            ],
            [
                'ticket_number' => 'TK-DEMO-0002',
                'title' => '[DEMO] Internet kantor lambat',
                'category' => 'Internet Lambat',
                'priority' => 'Normal',
                'status' => 'Assigned',
                'sla_due_at' => $now->copy()->addHours(8),
                'resolved_at' => null,
                'technician' => $assignedTechnicians[0],
            ],
            [
                'ticket_number' => 'TK-DEMO-0003',
                'title' => '[DEMO] Pemeriksaan perangkat ONT',
                'category' => 'Perangkat ONT Rusak',
                'priority' => 'High',
                'status' => 'In Progress',
                'sla_due_at' => $now->copy()->addHours(4),
                'resolved_at' => null,
                'technician' => $assignedTechnicians[1],
            ],
            [
                'ticket_number' => 'TK-DEMO-0004',
                'title' => '[DEMO] Gangguan jaringan selesai diperbaiki',
                'category' => 'Koneksi Putus',
                'priority' => 'Low',
                'status' => 'Resolved',
                'sla_due_at' => $now->copy()->subHours(2),
                'resolved_at' => $now->copy()->subHour(),
                'technician' => null,
            ],
        ];

        DB::transaction(function () use ($adminId, $customer, $demoTickets, $now): void {
            foreach ($demoTickets as $demoTicket) {
                /** @var Technician|null $technician */
                $technician = $demoTicket['technician'];

                $ticket = Ticket::updateOrCreate(
                    ['ticket_number' => $demoTicket['ticket_number']],
                    [
                        'customer_id' => $customer->id,
                        'customer_name' => $customer->name,
                        'created_by' => $adminId,
                        'reporter_name' => $customer->reporter_name ?: 'Pelapor Demo',
                        'reporter_contact' => $customer->reporter_contact,
                        'title' => $demoTicket['title'],
                        'subtitle' => $demoTicket['title'],
                        'issue_category' => $demoTicket['category'],
                        'description' => 'Data contoh untuk demonstrasi alur ticket pada OPD.',
                        'location' => $customer->area ?: 'Kantor OPD',
                        'priority' => $demoTicket['priority'],
                        'status' => $demoTicket['status'],
                        'assigned_tech_name' => $technician?->name,
                        'sla_due_at' => $demoTicket['sla_due_at'],
                        'resolved_at' => $demoTicket['resolved_at'],
                    ]
                );

                if ($technician) {
                    TicketAssignment::updateOrCreate(
                        [
                            'ticket_id' => $ticket->id,
                            'technician_id' => $technician->id,
                        ],
                        [
                            'assigned_by' => $adminId,
                            'assigned_at' => $now,
                            'unassigned_at' => null,
                            'is_active' => true,
                        ]
                    );

                    $technician->update(['status' => 'On Task']);
                }
            }
        });
    }
}
