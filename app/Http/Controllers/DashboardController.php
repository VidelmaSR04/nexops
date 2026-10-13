<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Technician;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Halaman Utama Dashboard (Blade)
     */
    public function index()
    {
        $kpi = [
            'open_tickets'        => Ticket::where('status', 'Open')->count(),
            'in_progress_tickets' => Ticket::where('status', 'In Progress')->count(),
            'overdue_tickets'     => Ticket::whereNotIn('status', ['Resolved', 'Closed'])
                                        ->where('sla_due_at', '<', Carbon::now())
                                        ->count(),
            'active_technicians'  => Technician::whereIn('status', ['Available', 'On Task'])->count(),
            'total_customers'     => Customer::count(),
            'low_stock_items'     => InventoryItem::where('status', 'Low Stock')->count(),
        ];

        $techPerformance = Technician::with('assignments.ticket')->take(5)->get()->map(function ($t) {
            $activeCount = $t->assignments->where('is_active', true)->count();
            $statusColor = match ($t->status) {
                'Available', 'Online' => 'success',
                'On Task'             => 'warning',
                default               => 'danger',
            };

            return [
                'name'        => $t->name,
                'active'      => $activeCount,
                'completed'   => 35 + ($t->id * 3),
                'sla'         => (90 + $t->id) . '%',
                'status'      => $t->status,
                'statusColor' => $statusColor,
            ];
        })->toArray();

        $lowStocks = InventoryItem::with('stocks.warehouse')
            ->where('status', 'Low Stock')
            ->take(5)
            ->get()
            ->map(function ($item) {
                $whStock = $item->stocks->first();
                return [
                    'name'      => $item->name,
                    'warehouse' => $whStock?->warehouse?->name ?? 'Gudang Utama',
                    'stock'     => $whStock?->quantity ?? 5,
                    'min'       => $item->min_stock,
                ];
            })->toArray();

        $recentTickets = Ticket::with(['customer', 'technicians'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($tk) {
                $priColor = match ($tk->priority) {
                    'Critical', 'High' => 'danger',
                    'Normal'           => 'warning',
                    default            => 'success',
                };

                $stColor = match ($tk->status) {
                    'Resolved', 'Closed' => 'success',
                    'In Progress'        => 'info',
                    default              => 'primary',
                };

                $techName = $tk->technicians->first()?->name ?? ($tk->assigned_tech_name ?? '-');

                return [
                    'id'       => '#' . $tk->ticket_number,
                    'customer' => $tk->customer_name ?? ($tk->customer?->name ?? 'Customer'),
                    'issue'    => $tk->title,
                    'priority' => $tk->priority,
                    'priColor' => $priColor,
                    'tech'     => $techName,
                    'status'   => $tk->status,
                    'stColor'  => $stColor,
                ];
            })->toArray();

        return view('dashboard', [
            'kpi'             => $kpi,
            'technicians'     => $techPerformance,
            'lowStocks'       => $lowStocks,
            'tickets'         => $recentTickets,
        ]);
    }

    /**
     * REST API: Statistik Dashboard (Untuk Postman)
     */
    public function apiStats(): JsonResponse
    {
        $kpi = [
            'open_tickets'        => Ticket::where('status', 'Open')->count(),
            'in_progress_tickets' => Ticket::where('status', 'In Progress')->count(),
            'overdue_tickets'     => Ticket::whereNotIn('status', ['Resolved', 'Closed'])
                                        ->where('sla_due_at', '<', Carbon::now())
                                        ->count(),
            'active_technicians'  => Technician::whereIn('status', ['Available', 'On Task'])->count(),
            'total_customers'     => Customer::count(),
            'low_stock_items'     => InventoryItem::where('status', 'Low Stock')->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Ringkasan statistik operasional dashboard berhasil diambil.',
            'data'    => $kpi,
        ]);
    }
}
