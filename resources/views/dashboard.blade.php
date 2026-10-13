<x-app-layout>
    <div class="space-y-6">

        <!-- ============================================ -->
        <!-- HEADER -->
        <!-- ============================================ -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-dark">
                    Good Morning, {{ explode(' ', Auth::user()->name)[0] }} 👋
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

            <x-kpi-card
                title="Open Ticket"
                value="42"
                icon="ticket"
                color="primary"
                trend="12%"
                :trend-up="true"
                subtitle="dari minggu lalu"
            />

            <x-kpi-card
                title="In Progress"
                value="18"
                icon="loader"
                color="info"
                trend="4%"
                :trend-up="true"
                subtitle="dari minggu lalu"
            />

            <x-kpi-card
                title="Overdue"
                value="7"
                icon="alert-circle"
                color="danger"
                trend="8%"
                :trend-up="false"
                subtitle="dari minggu lalu"
            />

            <x-kpi-card
                title="Low Stock"
                value="13"
                icon="package-x"
                color="warning"
                subtitle="⚠ Perlu tindakan"
            />

            <x-kpi-card
                title="Total Asset"
                value="1,284"
                icon="monitor"
                color="primary"
                trend="6%"
                :trend-up="true"
                subtitle="Aktif"
            />

            <x-kpi-card
                title="Technician"
                value="24"
                icon="hard-hat"
                color="success"
                subtitle="● Online 18"
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
                            <span class="text-2xl font-bold text-dark">141</span>
                            <span class="text-xs text-gray-400">Total Ticket</span>
                        </div>
                    </div>
                    <div class="space-y-2 text-xs w-full">
                        <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Open <span class="ml-auto font-medium text-gray-600">42</span> <span class="text-gray-400">30%</span></div>
                        <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-cyan-500"></span> Assigned <span class="ml-auto font-medium text-gray-600">18</span> <span class="text-gray-400">13%</span></div>
                        <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> In Progress <span class="ml-auto font-medium text-gray-600">15</span> <span class="text-gray-400">11%</span></div>
                        <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span> Waiting <span class="ml-auto font-medium text-gray-600">8</span> <span class="text-gray-400">6%</span></div>
                        <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Resolved <span class="ml-auto font-medium text-gray-600">31</span> <span class="text-gray-400">22%</span></div>
                        <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-slate-700"></span> Closed <span class="ml-auto font-medium text-gray-600">27</span> <span class="text-gray-400">19%</span></div>
                    </div>
                </div>
            </div>

            <!-- SLA Compliance -->
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h3 class="text-sm font-semibold text-dark mb-4">SLA Compliance</h3>
                <div class="text-center mb-4">
                    <span class="text-4xl font-bold text-dark">91%</span>
                    <p class="text-xs text-gray-400 mt-1">Sesuai SLA</p>
                </div>
                <!-- Response Time -->
                <div class="mb-3">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-500">Response Time</span>
                        <span class="font-semibold text-dark">94%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-success rounded-full h-2" style="width: 94%"></div>
                    </div>
                </div>
                <!-- Resolution Time -->
                <div class="mb-4">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-500">Resolution Time</span>
                        <span class="font-semibold text-dark">87%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-primary rounded-full h-2" style="width: 87%"></div>
                    </div>
                </div>
                <!-- MTTR -->
                <div class="border-t border-gray-100 pt-3 text-center">
                    <p class="text-xs text-gray-400">Rata-rata MTTR</p>
                    <p class="text-xl font-bold text-dark">02:34:21</p>
                    <div class="flex items-center justify-center gap-1 mt-1">
                        <i data-lucide="trending-up" class="w-3 h-3 text-success"></i>
                        <span class="text-xs text-success font-medium">12%</span>
                        <span class="text-xs text-gray-400">dari bulan lalu</span>
                    </div>
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
                    <a href="#" class="text-xs text-primary font-medium hover:underline">Lihat Semua →</a>
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
                        @php
                            $technicians = $technicians ?? [
                                ['name' => 'Ahmad', 'active' => 3, 'completed' => 42, 'sla' => '96%', 'status' => 'Online', 'statusColor' => 'success'],
                                ['name' => 'Budi', 'active' => 2, 'completed' => 38, 'sla' => '94%', 'status' => 'Online', 'statusColor' => 'success'],
                                ['name' => 'Dimas', 'active' => 4, 'completed' => 35, 'sla' => '91%', 'status' => 'Online', 'statusColor' => 'success'],
                                ['name' => 'Rizky', 'active' => 1, 'completed' => 28, 'sla' => '89%', 'status' => 'On Task', 'statusColor' => 'warning'],
                                ['name' => 'Fajar', 'active' => 3, 'completed' => 26, 'sla' => '87%', 'status' => 'Offline', 'statusColor' => 'danger'],
                            ];
                        @endphp
                        @foreach($technicians as $tech)
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
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Peta Operasional -->
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-dark">Peta Operasional</h3>
                    <span class="text-xs text-gray-400 bg-gray-50 px-2 py-1 rounded-md">Semua Tim</span>
                </div>
                <div class="relative bg-emerald-50 rounded-xl h-56 flex items-center justify-center overflow-hidden">
                    <!-- Placeholder Map -->
                    <div class="text-center">
                        <i data-lucide="map" class="w-12 h-12 text-emerald-300 mx-auto mb-2"></i>
                        <p class="text-sm text-emerald-600 font-medium">Peta Operasional</p>
                        <p class="text-xs text-emerald-400 mt-1">Situbondo • Besuki • Asembagus • Banyuglugur</p>
                    </div>
                </div>
                <div class="flex items-center justify-center gap-4 mt-3 text-xs text-gray-500">
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-success"></span> Teknisi Online</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-danger"></span> Ticket</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-primary"></span> Customer</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-purple-500"></span> Gudang</span>
                </div>
            </div>

            <!-- Aktivitas Terbaru -->
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-dark">Aktivitas Terbaru</h3>
                    <a href="#" class="text-xs text-primary font-medium hover:underline">Lihat Semua →</a>
                </div>
                <div class="space-y-4">
                    @php
                        $activities = [
                            ['time' => '10:15', 'icon' => 'check-circle', 'iconColor' => 'text-success', 'iconBg' => 'bg-emerald-50', 'title' => 'Ahmad menyelesaikan ticket #TK-1024', 'subtitle' => 'Internet restored • PT ABC'],
                            ['time' => '09:43', 'icon' => 'package', 'iconColor' => 'text-warning', 'iconBg' => 'bg-amber-50', 'title' => 'Stok berkurang', 'subtitle' => 'ONT ZTE F670L (-1) • Ticket #TK-1024'],
                            ['time' => '09:21', 'icon' => 'user-check', 'iconColor' => 'text-primary', 'iconBg' => 'bg-blue-50', 'title' => 'Ticket #TK-1025 ditugaskan ke Budi', 'subtitle' => 'Replace ONT • Customer #2391'],
                            ['time' => '08:56', 'icon' => 'clipboard-list', 'iconColor' => 'text-info', 'iconBg' => 'bg-cyan-50', 'title' => 'Permintaan material baru', 'subtitle' => 'Ahmad • 5 item • Menunggu approval'],
                            ['time' => '08:35', 'icon' => 'alert-triangle', 'iconColor' => 'text-danger', 'iconBg' => 'bg-red-50', 'title' => 'Ticket #TK-1023 overdue', 'subtitle' => 'SLA terlewati 1 jam'],
                        ];
                    @endphp
                    @foreach($activities as $act)
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
                    @endforeach
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
                    <a href="#" class="text-xs text-primary font-medium hover:underline">Lihat Semua →</a>
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
                        @php
                            $lowStocks = $lowStocks ?? [
                                ['name' => 'ONT ZTE F670L', 'warehouse' => 'Gudang Utama', 'stock' => 4, 'min' => 10],
                                ['name' => 'ONT Huawei HG8245H', 'warehouse' => 'Gudang Situbondo', 'stock' => 6, 'min' => 10],
                                ['name' => 'RJ45 Connector', 'warehouse' => 'Gudang Utama', 'stock' => 42, 'min' => 100],
                                ['name' => 'Fiber Patchcord', 'warehouse' => 'Gudang Besuki', 'stock' => 8, 'min' => 20],
                                ['name' => 'Adapter 12V', 'warehouse' => 'Gudang Utama', 'stock' => 12, 'min' => 25],
                            ];
                        @endphp
                        @foreach($lowStocks as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="py-2.5 font-medium text-dark">{{ $item['name'] }}</td>
                                <td class="py-2.5 text-gray-500">{{ $item['warehouse'] }}</td>
                                <td class="py-2.5 text-center font-semibold text-danger">{{ $item['stock'] }}</td>
                                <td class="py-2.5 text-center text-gray-500">{{ $item['min'] }}</td>
                                <td class="py-2.5 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-50 text-danger">Low Stock</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Ticket Terbaru -->
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-dark">Ticket Terbaru</h3>
                    <a href="#" class="text-xs text-primary font-medium hover:underline">Lihat Semua →</a>
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
                        @php
                            $tickets = $tickets ?? [
                                ['id' => '#TK-1025', 'customer' => 'Arkanet', 'issue' => 'Replace ONT', 'priority' => 'High', 'priColor' => 'danger', 'tech' => 'Budi', 'status' => 'Open', 'stColor' => 'primary'],
                                ['id' => '#TK-1024', 'customer' => 'PT ABC', 'issue' => 'Internet Down', 'priority' => 'High', 'priColor' => 'danger', 'tech' => 'Ahmad', 'status' => 'Resolved', 'stColor' => 'success'],
                                ['id' => '#TK-1023', 'customer' => 'Office', 'issue' => 'Router Problem', 'priority' => 'Normal', 'priColor' => 'warning', 'tech' => 'Dimas', 'status' => 'In Progress', 'stColor' => 'info'],
                                ['id' => '#TK-1022', 'customer' => 'Customer #2393', 'issue' => 'No Signal', 'priority' => 'Normal', 'priColor' => 'warning', 'tech' => 'Rizky', 'status' => 'Assigned', 'stColor' => 'primary'],
                                ['id' => '#TK-1021', 'customer' => 'Cafe 99', 'issue' => 'Speed Low', 'priority' => 'Low', 'priColor' => 'success', 'tech' => '-', 'status' => 'Open', 'stColor' => 'primary'],
                            ];
                        @endphp
                        @foreach($tickets as $t)
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
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <!-- ============================================ -->
    <!-- CHART.JS -->
    <!-- ============================================ -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
        // ===== TICKET TREND (Line Chart) =====
        new Chart(document.getElementById('ticketTrendChart'), {
            type: 'line',
            data: {
                labels: ['2 Okt', '3 Okt', '4 Okt', '5 Okt', '6 Okt', '7 Okt', '8 Okt'],
                datasets: [
                    {
                        label: 'Open',
                        data: [30, 35, 28, 38, 42, 40, 42],
                        borderColor: '#2563EB',
                        backgroundColor: 'rgba(37,99,235,0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 3,
                    },
                    {
                        label: 'In Progress',
                        data: [12, 15, 18, 14, 16, 20, 18],
                        borderColor: '#06B6D4',
                        backgroundColor: 'transparent',
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 3,
                    },
                    {
                        label: 'Resolved',
                        data: [20, 22, 25, 28, 30, 27, 31],
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
                labels: ['Open', 'Assigned', 'In Progress', 'Waiting', 'Resolved', 'Closed'],
                datasets: [{
                    data: [42, 18, 15, 8, 31, 27],
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

        // ===== LIVE CLOCK =====
        setInterval(() => {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('live-clock').textContent = h + ':' + m;
        }, 1000);
    </script>

</x-app-layout>
