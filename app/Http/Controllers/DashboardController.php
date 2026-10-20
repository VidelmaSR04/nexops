<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Asset;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Technician;
use App\Models\Ticket;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private const DONE_STATUSES   = ['Resolved', 'Closed'];
    private const ONLINE_TECH     = ['Available', 'Online', 'On Task'];
    private const TICKET_STATUSES = ['Open', 'Assigned', 'In Progress', 'Waiting', 'Resolved', 'Closed'];

    /**
     * Halaman Utama Dashboard (Blade)
     */
    public function index()
    {
        return view('dashboard', [
            'kpi'          => $this->kpi(),
            'trend'        => $this->ticketTrend(),
            'statusChart'  => $this->statusDistribution(),
            'sla'          => $this->slaStats(),
            'technicians'  => $this->technicianPerformance(),
            'activities'   => $this->recentActivities(),
            'lowStocks'    => $this->lowStockRows(),
            'tickets'      => $this->recentTickets(),
            'mapPoints'    => $this->mapPoints(),
        ]);
    }

    /**
     * REST API: Statistik Dashboard (Untuk Postman)
     */
    public function apiStats(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Ringkasan statistik operasional dashboard berhasil diambil.',
            'data'    => [
                'kpi'           => $this->kpi(),
                'ticket_status' => $this->statusDistribution(),
                'sla'           => $this->slaStats(),
            ],
        ]);
    }

    /* ------------------------------------------------------------------ */
    /*  KPI                                                                */
    /* ------------------------------------------------------------------ */

    private function kpi(): array
    {
        $thisWeek = Ticket::where('created_at', '>=', Carbon::now()->subDays(7))->count();
        $lastWeek = Ticket::whereBetween('created_at', [Carbon::now()->subDays(14), Carbon::now()->subDays(7)])->count();

        return [
            'open_tickets'        => Ticket::where('status', 'Open')->count(),
            'in_progress_tickets' => Ticket::where('status', 'In Progress')->count(),
            'overdue_tickets'     => Ticket::whereNotIn('status', self::DONE_STATUSES)
                                        ->whereNotNull('sla_due_at')
                                        ->where('sla_due_at', '<', Carbon::now())
                                        ->count(),
            'low_stock_items'     => $this->lowStockItems()->count(),
            'total_assets'        => Asset::count(),
            'total_technicians'   => Technician::count(),
            'online_technicians'  => Technician::whereIn('status', self::ONLINE_TECH)->count(),
            'total_customers'     => Customer::count(),
            'total_tickets'       => Ticket::count(),
            // perubahan jumlah ticket baru: 7 hari terakhir vs 7 hari sebelumnya (null = tidak bisa dihitung)
            'new_ticket_trend'    => $lastWeek > 0 ? (int) round(($thisWeek - $lastWeek) / $lastWeek * 100) : null,
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  CHART                                                              */
    /* ------------------------------------------------------------------ */

    /** Ticket 7 hari terakhir: dibuat / sedang dikerjakan / selesai per hari. */
    private function ticketTrend(): array
    {
        $labels = $open = $progress = $resolved = [];

        foreach (range(6, 0) as $i) {
            $day = Carbon::today()->subDays($i);

            $labels[]   = $day->locale('id')->isoFormat('D MMM');
            $open[]     = Ticket::whereDate('created_at', $day)->count();
            $progress[] = Ticket::whereDate('updated_at', $day)->where('status', 'In Progress')->count();
            $resolved[] = Ticket::whereDate('updated_at', $day)->whereIn('status', self::DONE_STATUSES)->count();
        }

        return compact('labels', 'open', 'progress', 'resolved');
    }

    /** Jumlah dan persentase ticket per status. */
    private function statusDistribution(): array
    {
        $counts = Ticket::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $grand = (int) $counts->sum();

        $items = collect(self::TICKET_STATUSES)->map(function ($status) use ($counts, $grand) {
            $count = (int) ($counts[$status] ?? 0);

            return [
                'status'  => $status,
                'count'   => $count,
                'percent' => $grand > 0 ? (int) round($count / $grand * 100) : 0,
            ];
        })->all();

        return ['total' => $grand, 'items' => $items];
    }

    /* ------------------------------------------------------------------ */
    /*  SLA                                                                */
    /* ------------------------------------------------------------------ */

    private function slaStats(): array
    {
        $now = Carbon::now();

        $withSla = Ticket::whereNotNull('sla_due_at')->get(['id', 'status', 'created_at', 'updated_at', 'sla_due_at']);
        $done    = $withSla->filter(fn ($t) => in_array($t->status, self::DONE_STATUSES));
        $active  = $withSla->reject(fn ($t) => in_array($t->status, self::DONE_STATUSES));

        $doneOnTime = $done->filter(fn ($t) => Carbon::parse($t->updated_at)->lte(Carbon::parse($t->sla_due_at)))->count();
        $activeOk   = $active->filter(fn ($t) => Carbon::parse($t->sla_due_at)->gte($now))->count();

        // Kepatuhan SLA keseluruhan
        $compliance = $withSla->count() > 0
            ? (int) round(($doneOnTime + $activeOk) / $withSla->count() * 100)
            : 0;

        // Ticket selesai tepat waktu
        $resolution = $done->count() > 0 ? (int) round($doneOnTime / $done->count() * 100) : 0;

        // Ticket yang sudah ditugaskan ke teknisi
        $totalTickets = Ticket::count();
        $assigned     = DB::table('ticket_assignments')->distinct()->count('ticket_id');
        $response     = $totalTickets > 0 ? (int) round($assigned / $totalTickets * 100) : 0;

        // MTTR: rata-rata durasi dari dibuat sampai selesai
        $avgSeconds = $done->avg(function ($t) {
            $finish = $t->resolved_at ?? $t->updated_at;
            return Carbon::parse($t->created_at)->diffInSeconds(Carbon::parse($finish));
        });
        $avgSeconds = (int) round($avgSeconds ?? 0);

        return [
            'compliance' => min($compliance, 100),
            'response'   => min($response, 100),
            'resolution' => min($resolution, 100),
            'mttr'       => sprintf('%02d:%02d:%02d', intdiv($avgSeconds, 3600), intdiv($avgSeconds % 3600, 60), $avgSeconds % 60),
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  TEKNISI                                                            */
    /* ------------------------------------------------------------------ */

    private function technicianPerformance(): array
    {
        return Technician::with('assignments.ticket')->get()->map(function ($t) {
            $active = $t->assignments->where('is_active', true)->count();

            $completedTickets = $t->assignments
                ->pluck('ticket')
                ->filter()
                ->unique('id')
                ->filter(fn ($tk) => in_array($tk->status, self::DONE_STATUSES));

            $onTime = $completedTickets->filter(
                fn ($tk) => $tk->sla_due_at && Carbon::parse($tk->updated_at)->lte(Carbon::parse($tk->sla_due_at))
            )->count();

            return [
                'name'        => $t->name,
                'active'      => $active,
                'completed'   => $completedTickets->count(),
                'sla'         => $completedTickets->count() > 0
                                    ? (int) round($onTime / $completedTickets->count() * 100) . '%'
                                    : '-',
                'status'      => $t->status,
                'statusColor' => match ($t->status) {
                    'Available', 'Online' => 'success',
                    'On Task'             => 'warning',
                    default               => 'danger',
                },
            ];
        })
        ->sortByDesc('completed')
        ->take(5)
        ->values()
        ->all();
    }

    /* ------------------------------------------------------------------ */
    /*  AKTIVITAS                                                          */
    /* ------------------------------------------------------------------ */

    private function recentActivities(): array
    {
        return ActivityLog::latest()->take(5)->get()->map(function ($log) {
            $text = $log->description ?? $log->activity ?? $log->action ?? 'Aktivitas';
            $who  = method_exists($log, 'user') ? optional($log->user)->name : null;
            $who  = $who ?? ($log->user_name ?? null);

            [$icon, $color, $bg] = $this->activityStyle($text);

            return [
                'time'      => Carbon::parse($log->created_at)->format('H:i'),
                'date'      => Carbon::parse($log->created_at)->locale('id')->isoFormat('D MMM'),
                'icon'      => $icon,
                'iconColor' => $color,
                'iconBg'    => $bg,
                'title'     => $text,
                'subtitle'  => $who ?: 'Sistem',
            ];
        })->all();
    }

    private function activityStyle(string $text): array
    {
        $t = strtolower($text);

        return match (true) {
            str_contains($t, 'selesai') || str_contains($t, 'resolved') || str_contains($t, 'closed')
                => ['check-circle', 'text-success', 'bg-emerald-50'],
            str_contains($t, 'stok') || str_contains($t, 'stock') || str_contains($t, 'material')
                => ['package', 'text-warning', 'bg-amber-50'],
            str_contains($t, 'tugas') || str_contains($t, 'assign')
                => ['user-check', 'text-primary', 'bg-blue-50'],
            str_contains($t, 'overdue') || str_contains($t, 'sla')
                => ['alert-triangle', 'text-danger', 'bg-red-50'],
            default
                => ['clipboard-list', 'text-info', 'bg-cyan-50'],
        };
    }

    /* ------------------------------------------------------------------ */
    /*  STOK RENDAH                                                        */
    /* ------------------------------------------------------------------ */

    /** Item dianggap low stock jika statusnya 'Low Stock' atau total stok <= minimum. */
    private function lowStockItems(): Collection
    {
        return InventoryItem::with('stocks.warehouse')
            ->withSum('stocks as total_stock', 'quantity')
            ->get()
            ->filter(function ($item) {
                $min = (int) ($item->min_stock ?? 0);

                return $item->status === 'Low Stock'
                    || ($min > 0 && (int) ($item->total_stock ?? 0) <= $min);
            })
            ->values();
    }

    private function lowStockRows(): array
    {
        return $this->lowStockItems()->take(5)->map(function ($item) {
            // gudang dengan stok paling sedikit
            $row = $item->stocks->sortBy('quantity')->first();

            return [
                'name'      => $item->name,
                'warehouse' => $row?->warehouse?->name ?? '-',
                'stock'     => (int) ($row?->quantity ?? $item->total_stock ?? 0),
                'min'       => (int) ($item->min_stock ?? 0),
            ];
        })->all();
    }

    /* ------------------------------------------------------------------ */
    /*  PETA OPERASIONAL                                                   */
    /* ------------------------------------------------------------------ */

    /**
     * Koordinat PERKIRAAN pusat tiap kecamatan di Situbondo.
     * Dipakai selama tabel belum punya kolom latitude/longitude.
     * Boleh ditambah/diubah sesuai wilayah operasionalmu.
     */
    private const AREA_COORDS = [
        'situbondo'   => [-7.7064, 114.0094],
        'besuki'      => [-7.7300, 113.7000],
        'banyuglugur' => [-7.7150, 113.8200],
        'panarukan'   => [-7.7000, 113.9400],
        'kendit'      => [-7.7500, 113.9900],
        'mangaran'    => [-7.7100, 114.0700],
        'arjasa'      => [-7.7000, 114.1400],
        'jangkar'     => [-7.7400, 114.1900],
        'asembagus'   => [-7.7700, 114.2200],
        'banyuputih'  => [-7.8200, 114.2900],
    ];

    /** Pusat peta bila lokasi tidak bisa dikenali. */
    private const MAP_DEFAULT = [-7.7064, 114.0094];

    /**
     * Cari koordinat: pakai kolom latitude/longitude jika ada,
     * kalau tidak cocokkan nama kecamatan di teks alamat/area,
     * kalau masih gagal taruh di sekitar pusat (digeser sedikit agar tidak menumpuk).
     */
    private function resolveCoords($model, int $seed = 0): array
    {
        $lat = $model->latitude ?? $model->lat ?? null;
        $lng = $model->longitude ?? $model->lng ?? $model->long ?? null;

        if (is_numeric($lat) && is_numeric($lng)) {
            return [(float) $lat, (float) $lng, true];
        }

        $text = strtolower(collect([
            $model->area ?? null, $model->address ?? null, $model->alamat ?? null,
            $model->location ?? null, $model->city ?? null, $model->name ?? null,
        ])->filter()->implode(' '));

        foreach (self::AREA_COORDS as $area => [$aLat, $aLng]) {
            if (str_contains($text, $area)) {
                return [$aLat + $this->jitter($seed, 1), $aLng + $this->jitter($seed, 2), false];
            }
        }

        return [self::MAP_DEFAULT[0] + $this->jitter($seed, 1), self::MAP_DEFAULT[1] + $this->jitter($seed, 2), false];
    }

    /** Pergeseran kecil & konsisten (±0.012°, sekitar 1 km) supaya marker tidak menumpuk. */
    private function jitter(int $seed, int $axis): float
    {
        return (((($seed * 37 + $axis * 17) % 25) - 12) / 1000);
    }

    private function mapPoints(): array
    {
        $points = [];

        // Customer
        foreach (Customer::take(100)->get() as $cu) {
            [$lat, $lng, $exact] = $this->resolveCoords($cu, $cu->id);
            $points[] = ['type' => 'customer', 'name' => $cu->name, 'info' => $cu->area ?? $cu->address ?? '', 'lat' => $lat, 'lng' => $lng, 'exact' => $exact];
        }

        // Gudang
        foreach (Warehouse::take(50)->get() as $wh) {
            [$lat, $lng, $exact] = $this->resolveCoords($wh, $wh->id);
            $points[] = ['type' => 'warehouse', 'name' => $wh->name, 'info' => $wh->location ?? $wh->address ?? '', 'lat' => $lat, 'lng' => $lng, 'exact' => $exact];
        }

        // Ticket aktif: lokasi mengikuti customer-nya
        $activeTickets = Ticket::with('customer')
            ->whereNotIn('status', self::DONE_STATUSES)
            ->take(100)
            ->get();

        foreach ($activeTickets as $tk) {
            $source = $tk->customer ?? $tk;
            [$lat, $lng, $exact] = $this->resolveCoords($source, $tk->id + 500);
            $points[] = ['type' => 'ticket', 'name' => '#' . $tk->ticket_number . ' - ' . $tk->title, 'info' => $tk->status . ' • ' . $tk->priority, 'lat' => $lat, 'lng' => $lng, 'exact' => $exact];
        }

        // Teknisi online: ikut lokasi customer dari ticket yang sedang dikerjakan
        $technicians = Technician::with('assignments.ticket.customer')
            ->whereIn('status', self::ONLINE_TECH)
            ->take(50)
            ->get();

        foreach ($technicians as $tc) {
            $activeAssignment = $tc->assignments->where('is_active', true)->first();
            $source = $activeAssignment?->ticket?->customer ?? $tc;
            [$lat, $lng, $exact] = $this->resolveCoords($source, $tc->id + 900);
            $points[] = ['type' => 'technician', 'name' => $tc->name, 'info' => $tc->status, 'lat' => $lat, 'lng' => $lng, 'exact' => $exact];
        }

        return $points;
    }

    /* ------------------------------------------------------------------ */
    /*  TICKET TERBARU                                                     */
    /* ------------------------------------------------------------------ */

    private function recentTickets(): array
    {
        return Ticket::with(['customer', 'technicians'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($tk) {
                return [
                    'id'       => '#' . $tk->ticket_number,
                    'customer' => $tk->customer_name ?? ($tk->customer?->name ?? '-'),
                    'issue'    => $tk->title,
                    'priority' => $tk->priority,
                    'priColor' => match ($tk->priority) {
                        'Critical', 'High' => 'danger',
                        'Normal'           => 'warning',
                        default            => 'success',
                    },
                    'tech'     => $tk->technicians->first()?->name ?? ($tk->assigned_tech_name ?? '-'),
                    'status'   => $tk->status,
                    'stColor'  => match ($tk->status) {
                        'Resolved', 'Closed' => 'success',
                        'In Progress'        => 'info',
                        default              => 'primary',
                    },
                ];
            })->all();
    }
}
