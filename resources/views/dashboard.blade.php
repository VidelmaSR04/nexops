<x-app-layout>
    <div class="space-y-6">

        <!-- ============================================ -->
        <!-- HEADER -->
        <!-- ============================================ -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-dark">
                    @php $h = now()->hour; $greet = $h < 11 ? 'Good Morning' : ($h < 15 ? 'Good Afternoon' : ($h < 19 ? 'Good Evening' : 'Good Night')); @endphp
                    {{ $greet }}, {{ explode(' ', Auth::user()->name)[0] }} 👋
                </h1>
                <p class="text-gray-500 text-sm mt-1">Berikut ringkasan operasional hari ini.</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">
                    {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                </p>
                <p class="text-2xl font-bold text-dark" id="live-clock">
                    {{ \Carbon\Carbon::now()->format('H:i') }}
                </p>
                <div class="flex items-center justify-end gap-1.5 mt-1">
                    <span class="w-2 h-2 rounded-full bg-success"></span>
                    <span class="text-xs text-gray-500">Sistem berjalan normal</span>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- KPI CARDS -->
        <!-- ============================================ -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">

            @if(!is_null($kpi['new_ticket_trend']))
                <x-kpi-card
                    title="Open Ticket"
                    :value="$kpi['open_tickets']"
                    icon="ticket"
                    color="primary"
                    :trend="abs($kpi['new_ticket_trend']) . '%'"
                    :trend-up="$kpi['new_ticket_trend'] >= 0"
                    subtitle="ticket baru vs minggu lalu"
                />
            @else
                <x-kpi-card
                    title="Open Ticket"
                    :value="$kpi['open_tickets']"
                    icon="ticket"
                    color="primary"
                    subtitle="Menunggu penanganan"
                />
            @endif

            <x-kpi-card
                title="In Progress"
                :value="$kpi['in_progress_tickets']"
                icon="loader"
                color="info"
                subtitle="Sedang dikerjakan"
            />

            <x-kpi-card
                title="Overdue"
                :value="$kpi['overdue_tickets']"
                icon="alert-circle"
                color="danger"
                subtitle="Melewati batas SLA"
            />

            <x-kpi-card
                title="Low Stock"
                :value="$kpi['low_stock_items']"
                icon="package-x"
                color="warning"
                :subtitle="$kpi['low_stock_items'] > 0 ? '⚠ Perlu tindakan' : 'Stok aman'"
            />

            <x-kpi-card
                title="Total Asset"
                :value="number_format($kpi['total_assets'])"
                icon="monitor"
                color="primary"
                subtitle="Terdaftar"
            />

            <x-kpi-card
                title="Technician"
                :value="$kpi['total_technicians']"
                icon="hard-hat"
                color="success"
                :subtitle="'● Online ' . $kpi['online_technicians']"
            />

        </div>

        <!-- ============================================ -->
        <!-- ROW 2: CHARTS -->
        <!-- ============================================ -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            <!-- Ticket Trend Chart -->
            <div class="bg-white rounded-2xl border border-gray-100 p-5 lg:col-span-1 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-dark">Ticket Trend</h3>
                        <p class="text-xs text-gray-400">Jumlah ticket dalam 7 hari terakhir</p>
                    </div>
                    <span class="text-xs text-gray-400 bg-gray-50 px-2 py-1 rounded-md">7 Hari Terakhir</span>
                </div>
                {{-- FIX: canvas dibungkus container bertinggi tetap --}}
                <div class="relative h-64 w-full">
                    <canvas id="ticketTrendChart"></canvas>
                </div>
            </div>

            <!-- Ticket Status Donut -->
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h3 class="text-sm font-semibold text-dark mb-4">Ticket Status</h3>
                <div class="flex flex-col sm:flex-row items-center gap-6">
                    <div class="relative w-40 h-40 shrink-0 mx-auto">
                        <canvas id="ticketStatusChart"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-2xl font-bold text-dark">{{ $statusChart['total'] }}</span>
                            <span class="text-xs text-gray-400">Total Ticket</span>
                        </div>
                    </div>
                    <div class="space-y-2 text-xs w-full">
                        @php
                            $dots = ['Open' => 'bg-blue-500', 'Assigned' => 'bg-cyan-500', 'In Progress' => 'bg-amber-500', 'Waiting' => 'bg-gray-400', 'Resolved' => 'bg-emerald-500', 'Closed' => 'bg-slate-700'];
                        @endphp
                        @foreach($statusChart['items'] as $s)
                            <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full {{ $dots[$s['status']] ?? 'bg-gray-300' }}"></span> {{ $s['status'] }} <span class="ml-auto font-medium text-gray-600">{{ $s['count'] }}</span> <span class="text-gray-400">{{ $s['percent'] }}%</span></div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- SLA Compliance -->
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h3 class="text-sm font-semibold text-dark mb-4">SLA Compliance</h3>
                <div class="text-center mb-4">
                    <span class="text-4xl font-bold text-dark">{{ $sla['compliance'] }}%</span>
                    <p class="text-xs text-gray-400 mt-1">Sesuai SLA</p>
                </div>
                <!-- Response Time -->
                <div class="mb-3">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-500">Sudah Ditugaskan</span>
                        <span class="font-semibold text-dark">{{ $sla['response'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-success rounded-full h-2" style="width: {{ $sla['response'] }}%"></div>
                    </div>
                </div>
                <!-- Resolution Time -->
                <div class="mb-4">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-500">Selesai Tepat Waktu</span>
                        <span class="font-semibold text-dark">{{ $sla['resolution'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-primary rounded-full h-2" style="width: {{ $sla['resolution'] }}%"></div>
                    </div>
                </div>
                <!-- MTTR -->
                <div class="border-t border-gray-100 pt-3 text-center">
                    <p class="text-xs text-gray-400">Rata-rata MTTR</p>
                    <p class="text-xl font-bold text-dark">{{ $sla['mttr'] }}</p>
                    <p class="text-[11px] text-gray-400 mt-1">Jam : Menit : Detik</p>
                </div>
            </div>

        </div>

        <!-- ============================================ -->
        <!-- ROW 3: TECHNICIAN + MAP + ACTIVITY -->
        <!-- ============================================ -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            <!-- Technician Performance -->
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-dark">Technician Performance</h3>
                    <a href="{{ route('technicians.index') }}" class="text-xs text-primary font-medium hover:underline">Lihat Semua →</a>
                </div>
                <table class="w-full text-xs">
                    <thead>
                        <tr class="text-gray-400 border-b border-gray-100">
                            <th class="text-left pb-2 font-medium">Technician</th>
                            <th class="text-center pb-2 font-medium">Active</th>
                            <th class="text-center pb-2 font-medium">Completed</th>
                            <th class="text-center pb-2 font-medium">SLA</th>
                            <th class="text-center pb-2 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">

                        @forelse($technicians as $tech)
                            <tr class="hover:bg-gray-50">
                                <td class="py-2.5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center">
                                            <span class="text-primary text-[10px] font-semibold">{{ substr($tech['name'], 0, 1) }}</span>
                                        </div>
                                        <span class="font-medium text-dark">{{ $tech['name'] }}</span>
                                    </div>
                                </td>
                                <td class="text-center text-gray-600">{{ $tech['active'] }}</td>
                                <td class="text-center text-gray-600">{{ $tech['completed'] }}</td>
                                <td class="text-center font-medium text-dark">{{ $tech['sla'] }}</td>
                                <td class="text-center">
                                    <span class="inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-{{ $tech['statusColor'] }}"></span>
                                        <span class="text-{{ $tech['statusColor'] }}">{{ $tech['status'] }}</span>
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-6 text-center text-gray-400">Belum ada data teknisi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Peta Operasional -->
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-dark">Peta Operasional</h3>
                    <span class="text-xs text-gray-400 bg-gray-50 px-2 py-1 rounded-md">Semua Tim</span>
                </div>
                <div id="ops-map" class="isolate relative rounded-xl h-64 w-full overflow-hidden border border-gray-100"></div>
                @php
                    $mapCount = collect($mapPoints)->countBy('type');
                @endphp
                <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1 mt-3 text-xs text-gray-500">
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full" style="background:#10B981"></span> Teknisi Online ({{ $mapCount['technician'] ?? 0 }})</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full" style="background:#EF4444"></span> Ticket ({{ $mapCount['ticket'] ?? 0 }})</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full" style="background:#2563EB"></span> Customer ({{ $mapCount['customer'] ?? 0 }})</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full" style="background:#A855F7"></span> Gudang ({{ $mapCount['warehouse'] ?? 0 }})</span>
                </div>
                <p class="text-[10px] text-gray-400 text-center mt-1">Posisi berdasarkan kecamatan (perkiraan) sampai koordinat tersedia.</p>
            </div>

            <!-- Aktivitas Terbaru -->
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-dark">Aktivitas Terbaru</h3>

                </div>
                <div class="space-y-4">

                    @forelse($activities as $act)
                        <div class="flex items-start gap-3">
                            <span class="text-xs text-gray-400 mt-0.5 w-10 shrink-0">{{ $act['time'] }}</span>
                            <div class="{{ $act['iconBg'] }} rounded-lg p-1.5 shrink-0">
                                <i data-lucide="{{ $act['icon'] }}" class="w-4 h-4 {{ $act['iconColor'] }}"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-dark">{{ $act['title'] }}</p>
                                <p class="text-xs text-gray-400">{{ $act['subtitle'] }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 text-center py-6">Belum ada aktivitas.</p>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- ============================================ -->
        <!-- ROW 4: LOW STOCK + RECENT TICKETS -->
        <!-- ============================================ -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

            <!-- Low Stock Items -->
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-dark">Low Stock Items</h3>
                    <a href="{{ route('inventory.index') }}" class="text-xs text-primary font-medium hover:underline">Lihat Semua →</a>
                </div>
                <table class="w-full text-xs">
                    <thead>
                        <tr class="text-gray-400 border-b border-gray-100">
                            <th class="text-left pb-2 font-medium">Produk</th>
                            <th class="text-left pb-2 font-medium">Gudang</th>
                            <th class="text-center pb-2 font-medium">Stok</th>
                            <th class="text-center pb-2 font-medium">Minimum</th>
                            <th class="text-center pb-2 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">

                        @forelse($lowStocks as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="py-2.5 font-medium text-dark">{{ $item['name'] }}</td>
                                <td class="py-2.5 text-gray-500">{{ $item['warehouse'] }}</td>
                                <td class="py-2.5 text-center font-semibold text-danger">{{ $item['stock'] }}</td>
                                <td class="py-2.5 text-center text-gray-500">{{ $item['min'] }}</td>
                                <td class="py-2.5 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-50 text-danger">Low Stock</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-6 text-center text-gray-400">Semua stok aman 🎉</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Ticket Terbaru -->
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-dark">Ticket Terbaru</h3>
                    <a href="{{ route('tickets.index') }}" class="text-xs text-primary font-medium hover:underline">Lihat Semua →</a>
                </div>
                <table class="w-full text-xs">
                    <thead>
                        <tr class="text-gray-400 border-b border-gray-100">
                            <th class="text-left pb-2 font-medium">ID</th>
                            <th class="text-left pb-2 font-medium">Customer</th>
                            <th class="text-left pb-2 font-medium">Issue</th>
                            <th class="text-center pb-2 font-medium">Priority</th>
                            <th class="text-left pb-2 font-medium">Technician</th>
                            <th class="text-center pb-2 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">

                        @forelse($tickets as $t)
                            <tr class="hover:bg-gray-50">
                                <td class="py-2.5 font-semibold text-primary">{{ $t['id'] }}</td>
                                <td class="py-2.5 text-dark">{{ $t['customer'] }}</td>
                                <td class="py-2.5 text-gray-500">{{ $t['issue'] }}</td>
                                <td class="py-2.5 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                                        {{ $t['priColor'] === 'danger' ? 'bg-red-50 text-danger' : '' }}
                                        {{ $t['priColor'] === 'warning' ? 'bg-amber-50 text-warning' : '' }}
                                        {{ $t['priColor'] === 'success' ? 'bg-emerald-50 text-success' : '' }}
                                    ">{{ $t['priority'] }}</span>
                                </td>
                                <td class="py-2.5 text-gray-600">{{ $t['tech'] }}</td>
                                <td class="py-2.5 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                                        {{ $t['stColor'] === 'primary' ? 'bg-blue-50 text-primary' : '' }}
                                        {{ $t['stColor'] === 'success' ? 'bg-emerald-50 text-success' : '' }}
                                        {{ $t['stColor'] === 'info' ? 'bg-cyan-50 text-info' : '' }}
                                    ">{{ $t['status'] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-6 text-center text-gray-400">Belum ada ticket.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <!-- ============================================ -->
    <!-- CHART.JS -->
    <!-- ============================================ -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
        // ===== TICKET TREND (Line Chart) =====
        new Chart(document.getElementById('ticketTrendChart'), {
            type: 'line',
            data: {
                labels: @json($trend['labels']),
                datasets: [
                    {
                        label: 'Ticket Baru',
                        data: @json($trend['open']),
                        borderColor: '#2563EB',
                        backgroundColor: 'rgba(37,99,235,0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 3,
                    },
                    {
                        label: 'In Progress',
                        data: @json($trend['progress']),
                        borderColor: '#06B6D4',
                        backgroundColor: 'transparent',
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 3,
                    },
                    {
                        label: 'Resolved',
                        data: @json($trend['resolved']),
                        borderColor: '#10B981',
                        backgroundColor: 'transparent',
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 3,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // ikut tinggi container (h-64)
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: { boxWidth: 8, usePointStyle: true, pointStyle: 'circle', font: { size: 11 } }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                    y: { beginAtZero: true, grid: { color: '#F1F5F9' }, ticks: { font: { size: 10 } } }
                }
            }
        });

        // ===== TICKET STATUS (Donut Chart) =====
        new Chart(document.getElementById('ticketStatusChart'), {
            type: 'doughnut',
            data: {
                labels: @json(collect($statusChart['items'])->pluck('status')),
                datasets: [{
                    data: @json(collect($statusChart['items'])->pluck('count')),
                    backgroundColor: ['#3B82F6', '#06B6D4', '#F59E0B', '#9CA3AF', '#10B981', '#334155'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: { legend: { display: false } }
            }
        });

        // ===== PETA OPERASIONAL (Leaflet + OpenStreetMap) =====
        (function () {
            const points = @json($mapPoints);
            const map = L.map('ops-map', { scrollWheelZoom: false }).setView([-7.7064, 114.0094], 10);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const colors = { technician: '#10B981', ticket: '#EF4444', customer: '#2563EB', warehouse: '#A855F7' };
            const labels = { technician: 'Teknisi', ticket: 'Ticket', customer: 'Customer', warehouse: 'Gudang' };
            const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
            const bounds = [];

            points.forEach(p => {
                L.circleMarker([p.lat, p.lng], {
                    radius: p.type === 'warehouse' ? 8 : 6,
                    color: '#fff', weight: 2,
                    fillColor: colors[p.type] || '#64748B', fillOpacity: 0.95
                })
                .addTo(map)
                .bindPopup(`<strong>${esc(p.name)}</strong><br><span style="color:#64748B">${labels[p.type]}${p.info ? ' • ' + esc(p.info) : ''}</span>`);
                bounds.push([p.lat, p.lng]);
            });

            if (bounds.length > 1) map.fitBounds(bounds, { padding: [24, 24], maxZoom: 12 });

            // aktifkan zoom scroll hanya setelah peta diklik, supaya scroll halaman tidak terganggu
            map.on('click', () => map.scrollWheelZoom.enable());
            map.on('mouseout', () => map.scrollWheelZoom.disable());
        })();

        // ===== LIVE CLOCK =====
        setInterval(() => {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('live-clock').textContent = h + ':' + m;
        }, 1000);
    </script>

</x-app-layout>
