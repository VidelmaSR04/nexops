<x-app-layout>

    {{-- ============================================ --}}
    {{-- DATA DUMMY TIKET (Frontend Prototype)         --}}
    {{-- ============================================ --}}
    @php
        $rawTickets = [
            [
                'id' => '#TK-1025', 'customer' => 'Arkanet', 'location' => 'Situbondo', 'title' => 'Replace ONT', 'subtitle' => 'Ganti ONT ZTE F670L',
                'priority' => 'High', 'status' => 'Open', 'tech' => 'Budi', 'created_at' => '08 Okt 2026 08:55',
                'description' => 'Ganti ONT ZTE F670L yang mengalami kerusakan. Pelanggan sudah melaporkan sejak pagi.',
                'asset' => 'ONT ZTE F670L', 'serial' => 'ZTE001234', 'sla_due' => '08 Okt 2026 12:55', 'sla_left' => '4 jam lagi',
                'materials' => [['name' => 'ONT ZTE F670L', 'qty' => 1, 'unit' => 'pcs'], ['name' => 'Fiber Patchcord 3m', 'qty' => 1, 'unit' => 'pcs']],
                'attachments' => [['name' => 'foto_ont_los.jpg', 'size' => '1.4 MB']]
            ],
            [
                'id' => '#TK-1024', 'customer' => 'PT ABC', 'location' => 'Besuki', 'title' => 'Internet Down', 'subtitle' => 'Internet kantor mati total',
                'priority' => 'High', 'status' => 'Resolved', 'tech' => 'Ahmad', 'created_at' => '08 Okt 2026 08:20',
                'description' => 'Koneksi internet kantor mati total sejak pagi. Teknisi telah menyambung kabel drop fiber yang putus.',
                'asset' => 'ONT ZTE F670L', 'serial' => 'ZTE001198', 'sla_due' => '08 Okt 2026 12:20', 'sla_left' => 'Selesai tepat waktu',
                'materials' => [['name' => 'Protection Sleeve', 'qty' => 2, 'unit' => 'pcs']],
                'attachments' => [['name' => 'laporan_pekerjaan.pdf', 'size' => '540 KB']]
            ],
            [
                'id' => '#TK-1023', 'customer' => 'Office', 'location' => 'Asembagus', 'title' => 'Router Problem', 'subtitle' => 'Router sering restart sendiri',
                'priority' => 'Normal', 'status' => 'In Progress', 'tech' => 'Dimas', 'created_at' => '08 Okt 2026 07:45',
                'description' => 'Router sering hang dan perlu direstart manual beberapa kali sehari saat beban kerja tinggi.',
                'asset' => 'Router TP-Link Archer C6', 'serial' => 'TPL552210', 'sla_due' => '08 Okt 2026 15:45', 'sla_left' => '6 jam lagi',
                'materials' => [], 'attachments' => []
            ],
            [
                'id' => '#TK-1022', 'customer' => 'Customer #2393', 'location' => 'Banyuglugur', 'title' => 'No Signal', 'subtitle' => 'Lampu indikator LOS merah',
                'priority' => 'Normal', 'status' => 'Assigned', 'tech' => 'Rizky', 'created_at' => '08 Okt 2026 07:20',
                'description' => 'Lampu LOS menyala merah, tidak ada sinyal dari core kabel fiber optik pelanggan.',
                'asset' => 'ONT Huawei HG8245H', 'serial' => 'HWT778120', 'sla_due' => '08 Okt 2026 15:20', 'sla_left' => '7 jam lagi',
                'materials' => [], 'attachments' => []
            ],
            [
                'id' => '#TK-1021', 'customer' => 'Cafe 99', 'location' => 'Situbondo', 'title' => 'Speed Low', 'subtitle' => 'Kecepatan jauh di bawah paket',
                'priority' => 'Low', 'status' => 'Open', 'tech' => '-', 'created_at' => '08 Okt 2026 06:30',
                'description' => 'Kecepatan internet drop saat ramai pelanggan, terutama di jam sibuk sore hari.',
                'asset' => 'ONT ZTE F670L', 'serial' => 'ZTE001302', 'sla_due' => '09 Okt 2026 06:30', 'sla_left' => '22 jam lagi',
                'materials' => [], 'attachments' => []
            ],
            [
                'id' => '#TK-1020', 'customer' => 'PT Sinar', 'location' => 'Besuki', 'title' => 'Network Problem', 'subtitle' => 'Packet loss tinggi di switch lantai 2',
                'priority' => 'High', 'status' => 'Waiting', 'tech' => 'Budi', 'created_at' => '07 Okt 2026 22:15',
                'description' => 'Jaringan kantor tersendat. Menunggu pengiriman kabel patchcord baru dari gudang pusat.',
                'asset' => 'Switch TP-Link SG1016', 'serial' => 'TPS330915', 'sla_due' => '08 Okt 2026 06:15', 'sla_left' => 'Menunggu material',
                'materials' => [], 'attachments' => []
            ],
            [
                'id' => '#TK-1019', 'customer' => 'Hotel Indah', 'location' => 'Asembagus', 'title' => 'WiFi Down', 'subtitle' => 'Access Point area lobby mati',
                'priority' => 'Normal', 'status' => 'Resolved', 'tech' => 'Ahmad', 'created_at' => '07 Okt 2026 20:42',
                'description' => 'WiFi area lobby tidak bisa diakses tamu hotel. Adaptor PoE diganti baru dan access point normal.',
                'asset' => 'Ubiquiti UAP-AC-LR', 'serial' => 'UBQ440021', 'sla_due' => '08 Okt 2026 04:42', 'sla_left' => 'Selesai',
                'materials' => [['name' => 'PoE Injector 24V', 'qty' => 1, 'unit' => 'pcs']], 'attachments' => []
            ],
            [
                'id' => '#TK-1018', 'customer' => 'UD Maju', 'location' => 'Situbondo', 'title' => 'Perangkat Rusak', 'subtitle' => 'ONT mati total kena petir',
                'priority' => 'Critical', 'status' => 'Open', 'tech' => 'Dimas', 'created_at' => '07 Okt 2026 18:30',
                'description' => 'Perangkat utama mati total setelah hujan petir kemarin sore. Seluruh transaksi kasir terhambat.',
                'asset' => 'ONT Huawei HG8245H', 'serial' => 'HWT778455', 'sla_due' => '07 Okt 2026 20:30', 'sla_left' => 'SLA Overdue',
                'materials' => [], 'attachments' => []
            ],
            [
                'id' => '#TK-1017', 'customer' => 'Klinik Sehat', 'location' => 'Besuki', 'title' => 'Internet Lambat', 'subtitle' => 'Akses rekam medis lambat',
                'priority' => 'Normal', 'status' => 'Assigned', 'tech' => 'Rizky', 'created_at' => '07 Okt 2026 16:20',
                'description' => 'Akses sistem rekam medis BPJS sangat lambat pada jam pelayanan pasien.',
                'asset' => 'ONT ZTE F670L', 'serial' => 'ZTE001077', 'sla_due' => '08 Okt 2026 00:20', 'sla_left' => 'Dalam proses',
                'materials' => [], 'attachments' => []
            ],
            [
                'id' => '#TK-1016', 'customer' => 'Koperasi ABC', 'location' => 'Banyuglugur', 'title' => 'Ganti Kabel', 'subtitle' => 'Kabel luar ruangan terputus',
                'priority' => 'Low', 'status' => 'Closed', 'tech' => 'Fajar', 'created_at' => '07 Okt 2026 14:10',
                'description' => 'Kabel dropcore 1 core putus terkena dahan pohon. Sudah dilakukan penarikan kabel baru 85 meter.',
                'asset' => 'Kabel Dropcore Fiber', 'serial' => '-', 'sla_due' => '08 Okt 2026 14:10', 'sla_left' => 'Tiket ditutup',
                'materials' => [['name' => 'Kabel Dropcore 1C', 'qty' => 85, 'unit' => 'meter']], 'attachments' => []
            ],
        ];

        $ticketsById = collect($rawTickets)->keyBy('id')->all();
        $technicians = ['Ahmad', 'Budi', 'Dimas', 'Rizky', 'Fajar'];
    @endphp

    <div class="space-y-6">

        {{-- ============================================ --}}
        {{-- 1. HEADER HALAMAN TICKET                     --}}
        {{-- ============================================ --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-dark tracking-tight">Ticket</h1>
                <p class="text-secondary text-sm mt-0.5">Kelola dan pantau semua ticket operasional</p>
            </div>
            <div>
                <button type="button" id="btn-open-create-modal"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-white text-sm font-medium hover:bg-blue-700 shadow-sm transition-colors">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Buat Ticket</span>
                </button>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- 2. SUMMARY CARDS                             --}}
        {{-- ============================================ --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Open --}}
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i data-lucide="ticket" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Open</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">42</p>
                </div>
            </div>

            {{-- In Progress --}}
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center shrink-0">
                    <i data-lucide="loader-2" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">In Progress</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">18</p>
                </div>
            </div>

            {{-- Overdue --}}
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Overdue</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">7</p>
                </div>
            </div>

            {{-- Resolved --}}
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Resolved</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">31</p>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- 3. LAYOUT 2-KOLOM (TABEL + PANEL DETAIL)     --}}
        {{-- ============================================ --}}
        <div class="flex flex-col xl:flex-row items-start gap-5">

            {{-- ======================================== --}}
            {{-- KOLOM KIRI: TABEL TICKET                 --}}
            {{-- ======================================== --}}
            <div id="table-container" class="w-full xl:flex-1 bg-white rounded-2xl border border-border shadow-[0_2px_6px_rgba(0,0,0,0.02)] overflow-hidden transition-all duration-300">

                {{-- Toolbar Filter & Search --}}
                <div class="p-4 border-b border-border space-y-3">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="relative flex-1 min-w-[220px]">
                            <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                            <input id="ticket-search" type="text"
                                   placeholder="Cari nomor ticket, customer, atau masalah..."
                                   class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-border bg-surface focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        </div>

                        <button type="button" id="btn-toggle-filters"
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-border text-xs font-medium text-dark hover:bg-gray-50 transition-colors">
                            <i data-lucide="filter" class="w-3.5 h-3.5 text-secondary"></i>
                            <span>Filter</span>
                            <i data-lucide="chevron-down" class="w-3 h-3 text-gray-400"></i>
                        </button>

                        <select id="ticket-sort"
                                class="text-xs rounded-xl border border-border bg-white px-3 py-2 text-dark focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <option value="desc">Terbaru</option>
                            <option value="asc">Terlama</option>
                        </select>
                    </div>

                    {{-- Dropdown Panel Filter Tambahan --}}
                    <div id="filter-dropdown-panel" class="hidden grid-cols-1 sm:grid-cols-4 gap-2.5 pt-2 border-t border-gray-100">
                        <select id="filter-status" class="text-xs rounded-lg border border-border bg-white px-2.5 py-1.5 text-dark">
                            <option value="">Semua Status</option>
                            <option value="Open">Open</option>
                            <option value="Assigned">Assigned</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Waiting">Waiting</option>
                            <option value="Resolved">Resolved</option>
                            <option value="Closed">Closed</option>
                        </select>

                        <select id="filter-priority" class="text-xs rounded-lg border border-border bg-white px-2.5 py-1.5 text-dark">
                            <option value="">Semua Prioritas</option>
                            <option value="Low">Low</option>
                            <option value="Normal">Normal</option>
                            <option value="High">High</option>
                            <option value="Critical">Critical</option>
                        </select>

                        <select id="filter-tech" class="text-xs rounded-lg border border-border bg-white px-2.5 py-1.5 text-dark">
                            <option value="">Semua Teknisi</option>
                            @foreach ($technicians as $tn)
                                <option value="{{ $tn }}">{{ $tn }}</option>
                            @endforeach
                            <option value="-">Belum ditugaskan</option>
                        </select>

                        <button type="button" id="btn-reset-filters"
                                class="text-xs rounded-lg border border-border px-3 py-1.5 text-secondary hover:bg-gray-50 text-center font-medium">
                            Reset Filter
                        </button>
                    </div>
                </div>

                {{-- Tabel Data --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr class="text-secondary bg-[#FAFCFF] border-b border-border font-medium text-[11px] uppercase tracking-wider">
                                <th class="pl-4 py-3 w-8">
                                    <input id="check-all" type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary/20">
                                </th>
                                <th class="py-3 pr-3">No. Ticket</th>
                                <th class="py-3 pr-3">Customer</th>
                                <th class="py-3 pr-3">Judul</th>
                                <th class="py-3 pr-3">Prioritas</th>
                                <th class="py-3 pr-3">Status</th>
                                <th class="py-3 pr-3">Teknisi</th>
                                <th class="py-3 pr-3 whitespace-nowrap">Tanggal</th>
                                <th class="py-3 pr-4 text-center w-8">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="ticket-table-body" class="divide-y divide-gray-100">
                            @foreach ($rawTickets as $idx => $t)
                                <tr class="ticket-row cursor-pointer transition-colors hover:bg-blue-50/40 {{ $idx === 0 ? 'bg-blue-50/70 border-l-4 border-l-primary' : '' }}"
                                    data-id="{{ $t['id'] }}"
                                    data-status="{{ $t['status'] }}"
                                    data-priority="{{ $t['priority'] }}"
                                    data-tech="{{ $t['tech'] }}"
                                    data-search="{{ strtolower($t['id'] . ' ' . $t['customer'] . ' ' . $t['title'] . ' ' . $t['subtitle']) }}">
                                    <td class="pl-4 py-3">
                                        <input type="checkbox" class="row-checkbox rounded border-gray-300 text-primary focus:ring-primary/20">
                                    </td>
                                    <td class="py-3 pr-3 font-semibold text-primary whitespace-nowrap">
                                        {{ $t['id'] }}
                                    </td>
                                    <td class="py-3 pr-3">
                                        <p class="font-medium text-dark">{{ $t['customer'] }}</p>
                                        <p class="text-[11px] text-secondary">{{ $t['location'] }}</p>
                                    </td>
                                    <td class="py-3 pr-3">
                                        <p class="font-medium text-dark">{{ $t['title'] }}</p>
                                        <p class="text-[11px] text-secondary truncate max-w-[170px]">{{ $t['subtitle'] }}</p>
                                    </td>
                                    <td class="py-3 pr-3" data-col="priority">
                                        <x-priority-badge :priority="$t['priority']" />
                                    </td>
                                    <td class="py-3 pr-3" data-col="status">
                                        <x-status-badge :status="$t['status']" />
                                    </td>
                                    <td class="py-3 pr-3 whitespace-nowrap">
                                        @if ($t['tech'] === '-')
                                            <span class="text-gray-400">-</span>
                                        @else
                                            <div class="flex items-center gap-1.5">
                                                <div class="w-5 h-5 rounded-full bg-primary/10 text-primary text-[10px] font-bold flex items-center justify-center">
                                                    {{ substr($t['tech'], 0, 1) }}
                                                </div>
                                                <span class="text-dark">{{ $t['tech'] }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3 pr-3 whitespace-nowrap text-secondary text-[11px]">
                                        {{ $t['created_at'] }}
                                    </td>
                                    <td class="py-3 pr-4 text-center">
                                        <button type="button" class="p-1 rounded-md text-gray-400 hover:text-dark hover:bg-gray-100" data-dummy-action="Menu Aksi {{ $t['id'] }}">
                                            <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                            <tr id="empty-state-row" class="hidden">
                                <td colspan="9" class="py-12 text-center text-gray-400">
                                    <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                                    Tidak ada tiket yang cocok dengan pencarian / filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Dummy --}}
                <div class="p-3.5 border-t border-border flex flex-wrap items-center justify-between gap-3 text-xs">
                    <p id="table-info" class="text-secondary">Menampilkan 1-10 dari 142 ticket</p>
                    <div class="flex items-center gap-1">
                        <button type="button" class="w-7 h-7 rounded-lg border border-border flex items-center justify-center text-secondary hover:bg-gray-50" data-dummy-action="Halaman Sebelumnya">
                            <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
                        </button>
                        <button type="button" class="w-7 h-7 rounded-lg bg-primary text-white font-medium flex items-center justify-center">1</button>
                        <button type="button" class="w-7 h-7 rounded-lg border border-border text-secondary hover:bg-gray-50 flex items-center justify-center" data-dummy-action="Halaman 2">2</button>
                        <button type="button" class="w-7 h-7 rounded-lg border border-border text-secondary hover:bg-gray-50 flex items-center justify-center" data-dummy-action="Halaman 3">3</button>
                        <span class="px-1 text-gray-400">...</span>
                        <button type="button" class="w-7 h-7 rounded-lg border border-border text-secondary hover:bg-gray-50 flex items-center justify-center" data-dummy-action="Halaman 15">15</button>
                        <button type="button" class="w-7 h-7 rounded-lg border border-border flex items-center justify-center text-secondary hover:bg-gray-50" data-dummy-action="Halaman Berikutnya">
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>

            </div>

            {{-- ======================================== --}}
            {{-- KOLOM KANAN: PANEL DETAIL TICKET         --}}
            {{-- ======================================== --}}
            <aside id="ticket-detail-panel"
                   class="w-full xl:w-[480px] 2xl:w-[520px] bg-white rounded-2xl border border-border shadow-[0_2px_6px_rgba(0,0,0,0.02)] p-5 shrink-0 transition-all duration-300 xl:sticky xl:top-20">

                {{-- Header Detail Panel --}}
                <div class="flex items-start justify-between pb-4 border-b border-border">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span id="detail-ticket-id" class="text-lg font-bold text-primary">#TK-1025</span>
                            <span id="detail-priority-badge"><x-priority-badge priority="High" /></span>
                            <span id="detail-status-badge"><x-status-badge status="Open" /></span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-secondary">
                            <span id="detail-created-at">08 Okt 2026 08:55</span>
                            <span>•</span>
                            <span class="font-medium text-amber-600 flex items-center gap-1">
                                <i data-lucide="clock" class="w-3 h-3"></i> SLA: <span id="detail-sla-left">4 jam lagi</span>
                            </span>
                        </div>
                    </div>

                    <button type="button" id="btn-close-detail"
                            class="p-1.5 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100 transition-colors"
                            title="Tutup Panel Detail">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                {{-- Judul & Deskripsi Singkat --}}
                <div class="py-3.5">
                    <h2 id="detail-title" class="text-base font-bold text-dark">Replace ONT</h2>
                    <p id="detail-subtitle" class="text-xs text-secondary mt-1">Ganti ONT ZTE F670L yang mengalami kerusakan.</p>
                </div>

                {{-- Informasi Ringkas 4 Kolom Horizontal --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 p-3 rounded-xl bg-surface border border-border/80 text-xs mb-4">
                    <div>
                        <p class="text-[10px] text-secondary uppercase font-semibold">Customer</p>
                        <p id="detail-card-customer" class="font-bold text-dark mt-0.5 truncate">Arkanet</p>
                        <p id="detail-card-location" class="text-[10px] text-secondary">Situbondo</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-secondary uppercase font-semibold">Technician</p>
                        <p id="detail-card-tech" class="font-bold text-dark mt-0.5 truncate">Budi</p>
                        <p class="text-[10px] text-emerald-600 font-medium">On Task</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-secondary uppercase font-semibold">Asset</p>
                        <p id="detail-card-asset" class="font-bold text-dark mt-0.5 truncate">ONT ZTE F670L</p>
                        <p id="detail-card-serial" class="text-[10px] text-secondary truncate">SN: ZTE001234</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-secondary uppercase font-semibold">Priority</p>
                        <p id="detail-card-priority" class="font-bold text-red-600 mt-0.5">High</p>
                        <p class="text-[10px] text-secondary">Urgent</p>
                    </div>
                </div>

                {{-- 4 Tab Navigation --}}
                <div class="flex border-b border-border text-xs font-semibold mb-4 gap-6" id="panel-tabs">
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-primary text-primary transition-colors" data-target="tab-detail">Detail</button>
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-transparent text-secondary hover:text-dark transition-colors" data-target="tab-material">Material</button>
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-transparent text-secondary hover:text-dark transition-colors" data-target="tab-activity">Activity</button>
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-transparent text-secondary hover:text-dark transition-colors" data-target="tab-attachment">Attachment</button>
                </div>

                {{-- Konten Tab Panel --}}
                <div class="space-y-4">

                    {{-- TAB 1: DETAIL --}}
                    <div id="tab-detail" class="tab-pane space-y-3.5">
                        <div class="space-y-2.5 text-xs">
                            <div>
                                <span class="text-secondary text-[11px] block">Nomor Ticket:</span>
                                <span id="info-ticket-id" class="font-semibold text-dark">TK-1025</span>
                            </div>
                            <div>
                                <span class="text-secondary text-[11px] block">Judul:</span>
                                <span id="info-title" class="font-medium text-dark">Replace ONT</span>
                            </div>
                            <div>
                                <span class="text-secondary text-[11px] block">Deskripsi:</span>
                                <p id="info-description" class="text-dark bg-surface p-2.5 rounded-xl border border-border mt-1 leading-relaxed">
                                    Ganti ONT ZTE F670L yang mengalami kerusakan. Pelanggan sudah melaporkan sejak pagi.
                                </p>
                            </div>
                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div>
                                    <span class="text-secondary text-[11px] block">Customer:</span>
                                    <span id="info-customer" class="font-medium text-dark">Arkanet (Situbondo)</span>
                                </div>
                                <div>
                                    <span class="text-secondary text-[11px] block">Teknisi:</span>
                                    <span id="info-tech" class="font-medium text-dark">Budi</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div>
                                    <span class="text-secondary text-[11px] block">Created By:</span>
                                    <span class="font-medium text-dark">Eka Pratama (Administrator)</span>
                                </div>
                                <div>
                                    <span class="text-secondary text-[11px] block">SLA Due:</span>
                                    <span id="info-sla-due" class="font-medium text-dark">08 Okt 2026 12:55</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 2: MATERIAL --}}
                    <div id="tab-material" class="tab-pane hidden space-y-2.5 text-xs">
                        <p class="font-semibold text-dark text-xs mb-2">Material / Sparepart Digunakan:</p>
                        <ul id="material-list" class="space-y-2">
                            <li class="flex items-center justify-between p-2.5 rounded-xl border border-border bg-surface">
                                <span class="font-medium text-dark">ONT ZTE F670L</span>
                                <span class="text-secondary font-semibold">1 pcs</span>
                            </li>
                            <li class="flex items-center justify-between p-2.5 rounded-xl border border-border bg-surface">
                                <span class="font-medium text-dark">Fiber Patchcord 3m</span>
                                <span class="text-secondary font-semibold">1 pcs</span>
                            </li>
                        </ul>
                    </div>

                    {{-- TAB 3: ACTIVITY --}}
                    <div id="tab-activity" class="tab-pane hidden space-y-3 text-xs">
                        <p class="font-semibold text-dark text-xs mb-2">Riwayat Aktivitas Tiket:</p>
                        <div class="space-y-3 relative before:absolute before:inset-0 before:left-2 before:w-0.5 before:bg-gray-200">
                            <div class="relative flex items-start gap-3 pl-6">
                                <span class="absolute left-1 top-1 w-2.5 h-2.5 rounded-full bg-primary ring-4 ring-white"></span>
                                <div>
                                    <p class="font-semibold text-dark">Tiket Dibuat</p>
                                    <p class="text-[11px] text-secondary">Oleh Eka Pratama • 08 Okt 2026 08:55</p>
                                </div>
                            </div>
                            <div class="relative flex items-start gap-3 pl-6">
                                <span class="absolute left-1 top-1 w-2.5 h-2.5 rounded-full bg-cyan-500 ring-4 ring-white"></span>
                                <div>
                                    <p class="font-semibold text-dark">Teknisi Ditugaskan</p>
                                    <p class="text-[11px] text-secondary">Budi ditugaskan menangani kasus ini</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 4: ATTACHMENT --}}
                    <div id="tab-attachment" class="tab-pane hidden space-y-2.5 text-xs">
                        <p class="font-semibold text-dark text-xs mb-2">Lampiran File / Foto:</p>
                        <ul id="attachment-list" class="space-y-2">
                            <li class="flex items-center justify-between p-2.5 rounded-xl border border-border bg-surface">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="paperclip" class="w-4 h-4 text-secondary"></i>
                                    <span class="font-medium text-dark">foto_ont_los.jpg</span>
                                </div>
                                <span class="text-[11px] text-secondary">1.4 MB</span>
                            </li>
                        </ul>
                    </div>

                    {{-- Status & Aksi Card --}}
                    <div class="p-3.5 rounded-xl border border-border bg-surface space-y-3">
                        <p class="font-semibold text-dark text-xs">Status & Aksi</p>
                        <select id="action-status-select" class="w-full text-xs rounded-lg border border-border bg-white px-3 py-2 text-dark focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <option value="Open">Open</option>
                            <option value="Assigned">Assigned</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Waiting">Waiting</option>
                            <option value="Resolved">Resolved</option>
                            <option value="Closed">Closed</option>
                        </select>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" class="col-span-2 py-2 px-3 rounded-lg bg-primary text-white text-xs font-semibold hover:bg-blue-700 shadow-sm transition-colors text-center" data-dummy-action="Assign Teknisi">
                                Assign Teknisi
                            </button>
                            <button type="button" class="py-2 px-3 rounded-lg bg-white border border-border text-dark text-xs font-medium hover:bg-gray-50 transition-colors text-center" data-dummy-action="Ubah Status">
                                Ubah Status
                            </button>
                            <button type="button" class="py-2 px-3 rounded-lg bg-white border border-border text-dark text-xs font-medium hover:bg-gray-50 transition-colors text-center" data-dummy-action="Edit Ticket">
                                Edit Ticket
                            </button>
                            <button type="button" class="col-span-2 py-2 px-3 rounded-lg bg-white border border-red-200 text-danger text-xs font-medium hover:bg-red-50 transition-colors text-center" data-dummy-action="Tutup Ticket">
                                Tutup Ticket
                            </button>
                        </div>
                    </div>

                    {{-- Quick Info Card --}}
                    <div class="p-3.5 rounded-xl border border-border bg-surface space-y-2.5 text-xs">
                        <p class="font-semibold text-dark text-xs">Quick Info</p>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-secondary text-[11px]">Customer:</p>
                                <p id="quick-customer" class="font-medium text-dark">Arkanet</p>
                            </div>
                            <a href="#" class="text-primary text-[11px] font-semibold hover:underline" data-dummy-action="Detail Customer Arkanet">Lihat Detail</a>
                        </div>
                        <div class="flex items-center justify-between border-t border-gray-200/60 pt-2">
                            <div>
                                <p class="text-secondary text-[11px]">Asset Terkait:</p>
                                <p id="quick-asset" class="font-medium text-dark">ONT ZTE F670L</p>
                            </div>
                            <a href="#" class="text-primary text-[11px] font-semibold hover:underline" data-dummy-action="Detail Asset ONT ZTE">Lihat Detail</a>
                        </div>
                    </div>

                </div>

            </aside>

        </div>

    </div>

    {{-- ============================================ --}}
    {{-- 4. MODAL BUAT TICKET (FRONTEND PROTOTYPE)     --}}
    {{-- ============================================ --}}
    <div id="modal-create-ticket" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs hidden">
        <div class="bg-white rounded-2xl border border-border shadow-2xl max-w-lg w-full overflow-hidden animate-in fade-in duration-150">
            <div class="p-4 border-b border-border flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-dark">Buat Ticket Baru</h3>
                    <p class="text-secondary text-xs">Form pembuatan tiket operasional baru</p>
                </div>
                <button type="button" id="btn-close-modal" class="p-1 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form id="form-create-ticket" class="p-5 space-y-3.5 text-xs">
                <div>
                    <label class="block font-medium text-dark mb-1">Customer</label>
                    <input type="text" placeholder="Contoh: PT ABC / Arkanet" required
                           class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1">Lokasi</label>
                        <input type="text" placeholder="Situbondo" required
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Prioritas</label>
                        <select class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                            <option value="Low">Low</option>
                            <option value="Normal" selected>Normal</option>
                            <option value="High">High</option>
                            <option value="Critical">Critical</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1">Judul Masalah</label>
                    <input type="text" placeholder="Contoh: Internet Down / Replace ONT" required
                           class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1">Deskripsi Lengkap</label>
                    <textarea rows="3" placeholder="Jelaskan detail kendala..."
                              class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1">Teknisi</label>
                        <select class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                            <option value="-">Belum Ditugaskan</option>
                            @foreach ($technicians as $tn)
                                <option value="{{ $tn }}">{{ $tn }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Perangkat / Asset</label>
                        <input type="text" placeholder="ONT ZTE F670L"
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                </div>
                <div class="pt-3 border-t border-border flex items-center justify-end gap-2">
                    <button type="button" id="btn-cancel-modal" class="px-3.5 py-2 rounded-xl border border-border text-secondary hover:bg-gray-50 font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-blue-700 shadow-sm">Simpan Ticket</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Toast Notifikasi Dummy --}}
    <div id="toast-notification"
         class="fixed bottom-5 right-5 z-50 hidden max-w-sm rounded-xl bg-dark text-white text-xs px-4 py-3 shadow-xl transition-all">
    </div>

    {{-- ============================================ --}}
    {{-- 5. JAVASCRIPT LOGIC INTERAKTIF              --}}
    {{-- ============================================ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rawData = @json($ticketsById);

            // Toast helper
            let toastTimer;
            function showToast(message) {
                const toast = document.getElementById('toast-notification');
                toast.textContent = message;
                toast.classList.remove('hidden');
                clearTimeout(toastTimer);
                toastTimer = setTimeout(() => toast.classList.add('hidden'), 2500);
            }

            // Dummy Action Click Handler
            document.querySelectorAll('[data-dummy-action]').forEach(el => {
                el.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    showToast(el.getAttribute('data-dummy-action') + ' (Aksi UI Dummy)');
                });
            });

            // 1. Search & Filter & Sort
            const searchInput = document.getElementById('ticket-search');
            const statusFilter = document.getElementById('filter-status');
            const priorityFilter = document.getElementById('filter-priority');
            const techFilter = document.getElementById('filter-tech');
            const sortSelect = document.getElementById('ticket-sort');
            const rows = Array.from(document.querySelectorAll('.ticket-row'));
            const tbody = document.getElementById('ticket-table-body');
            const emptyState = document.getElementById('empty-state-row');
            const tableInfo = document.getElementById('table-info');

            function applyFilters() {
                const q = searchInput.value.toLowerCase().trim();
                const st = statusFilter.value;
                const pr = priorityFilter.value;
                const tc = techFilter.value;
                const sortAsc = sortSelect.value === 'asc';

                let visibleCount = 0;

                // Sorting
                const sortedRows = rows.slice().sort((a, b) => {
                    const idA = parseInt(a.dataset.id.replace('#TK-', ''));
                    const idB = parseInt(b.dataset.id.replace('#TK-', ''));
                    return sortAsc ? idA - idB : idB - idA;
                });

                sortedRows.forEach(r => tbody.insertBefore(r, emptyState));

                sortedRows.forEach(r => {
                    const matchQ = !q || r.dataset.search.includes(q);
                    const matchSt = !st || r.dataset.status === st;
                    const matchPr = !pr || r.dataset.priority === pr;
                    const matchTc = !tc || r.dataset.tech === tc;

                    const match = matchQ && matchSt && matchPr && matchTc;
                    r.classList.toggle('hidden', !match);
                    if (match) visibleCount++;
                });

                emptyState.classList.toggle('hidden', visibleCount > 0);
                tableInfo.textContent = `Menampilkan ${visibleCount} dari 142 ticket`;
            }

            [searchInput, statusFilter, priorityFilter, techFilter, sortSelect].forEach(el => {
                el.addEventListener(el === searchInput ? 'input' : 'change', applyFilters);
            });

            // Toggle Filter Box
            const toggleFiltersBtn = document.getElementById('btn-toggle-filters');
            const filterDropdown = document.getElementById('filter-dropdown-panel');
            toggleFiltersBtn.addEventListener('click', () => {
                filterDropdown.classList.toggle('hidden');
                filterDropdown.classList.toggle('grid');
            });

            document.getElementById('btn-reset-filters').addEventListener('click', () => {
                searchInput.value = '';
                statusFilter.value = '';
                priorityFilter.value = '';
                techFilter.value = '';
                sortSelect.value = 'desc';
                applyFilters();
            });

            // Check All
            const checkAll = document.getElementById('check-all');
            checkAll.addEventListener('change', (e) => {
                document.querySelectorAll('.row-checkbox').forEach(cb => {
                    if (!cb.closest('tr').classList.contains('hidden')) {
                        cb.checked = e.target.checked;
                    }
                });
            });

            // 2. Klik Baris Tabel -> Update Detail Panel
            const detailPanel = document.getElementById('ticket-detail-panel');

            function populateDetail(ticketId) {
                const t = rawData[ticketId];
                if (!t) return;

                document.getElementById('detail-ticket-id').textContent = t.id;
                document.getElementById('info-ticket-id').textContent = t.id.replace('#', '');
                document.getElementById('detail-created-at').textContent = t.created_at;
                document.getElementById('detail-sla-left').textContent = t.sla_left;
                document.getElementById('detail-title').textContent = t.title;
                document.getElementById('info-title').textContent = t.title;
                document.getElementById('detail-subtitle').textContent = t.subtitle;
                document.getElementById('info-description').textContent = t.description;

                document.getElementById('detail-card-customer').textContent = t.customer;
                document.getElementById('info-customer').textContent = `${t.customer} (${t.location})`;
                document.getElementById('quick-customer').textContent = t.customer;
                document.getElementById('detail-card-location').textContent = t.location;

                document.getElementById('detail-card-tech').textContent = t.tech;
                document.getElementById('info-tech').textContent = t.tech;

                document.getElementById('detail-card-asset').textContent = t.asset;
                document.getElementById('quick-asset').textContent = t.asset;
                document.getElementById('detail-card-serial').textContent = `SN: ${t.serial}`;
                document.getElementById('info-sla-due').textContent = t.sla_due;

                // Sync Badges from table row
                const row = document.querySelector(`.ticket-row[data-id="${ticketId}"]`);
                if (row) {
                    document.getElementById('detail-priority-badge').innerHTML = row.querySelector('[data-col="priority"]').innerHTML;
                    document.getElementById('detail-status-badge').innerHTML = row.querySelector('[data-col="status"]').innerHTML;
                    document.getElementById('detail-card-priority').textContent = t.priority;
                }

                document.getElementById('action-status-select').value = t.status;

                // Materials
                const matList = document.getElementById('material-list');
                matList.innerHTML = t.materials && t.materials.length
                    ? t.materials.map(m => `
                        <li class="flex items-center justify-between p-2.5 rounded-xl border border-border bg-surface">
                            <span class="font-medium text-dark">${m.name}</span>
                            <span class="text-secondary font-semibold">${m.qty} ${m.unit}</span>
                        </li>`).join('')
                    : '<li class="text-secondary py-3 text-center">Belum ada material digunakan.</li>';

                // Attachments
                const attList = document.getElementById('attachment-list');
                attList.innerHTML = t.attachments && t.attachments.length
                    ? t.attachments.map(a => `
                        <li class="flex items-center justify-between p-2.5 rounded-xl border border-border bg-surface">
                            <div class="flex items-center gap-2">
                                <i data-lucide="paperclip" class="w-4 h-4 text-secondary"></i>
                                <span class="font-medium text-dark">${a.name}</span>
                            </div>
                            <span class="text-[11px] text-secondary">${a.size}</span>
                        </li>`).join('')
                    : '<li class="text-secondary py-3 text-center">Tidak ada lampiran.</li>';

                if (window.lucide) window.lucide.createIcons();
            }

            rows.forEach(r => {
                r.addEventListener('click', (e) => {
                    if (e.target.closest('input, button')) return;
                    const id = r.dataset.id;

                    rows.forEach(other => {
                        other.classList.remove('bg-blue-50/70', 'border-l-4', 'border-l-primary');
                    });
                    r.classList.add('bg-blue-50/70', 'border-l-4', 'border-l-primary');

                    populateDetail(id);
                    detailPanel.classList.remove('hidden');
                });
            });

            // Tutup Panel Detail
            document.getElementById('btn-close-detail').addEventListener('click', () => {
                detailPanel.classList.add('hidden');
                rows.forEach(r => r.classList.remove('bg-blue-50/70', 'border-l-4', 'border-l-primary'));
            });

            // 3. Tab Switching di Detail Panel
            document.querySelectorAll('#panel-tabs .tab-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const targetId = btn.dataset.target;
                    document.querySelectorAll('#panel-tabs .tab-btn').forEach(b => {
                        b.classList.remove('border-primary', 'text-primary');
                        b.classList.add('border-transparent', 'text-secondary');
                    });
                    btn.classList.add('border-primary', 'text-primary');
                    btn.classList.remove('border-transparent', 'text-secondary');

                    document.querySelectorAll('.tab-pane').forEach(p => p.classList.add('hidden'));
                    document.getElementById(targetId).classList.remove('hidden');
                });
            });

            // 4. Modal Buat Ticket
            const modal = document.getElementById('modal-create-ticket');
            document.getElementById('btn-open-create-modal').addEventListener('click', () => {
                modal.classList.remove('hidden');
            });
            const closeModal = () => modal.classList.add('hidden');
            document.getElementById('btn-close-modal').addEventListener('click', closeModal);
            document.getElementById('btn-cancel-modal').addEventListener('click', closeModal);

            document.getElementById('form-create-ticket').addEventListener('click', (e) => {
                if (e.target.type === 'submit') {
                    e.preventDefault();
                    closeModal();
                    showToast('Ticket baru berhasil dibuat (Simulasi Frontend)!');
                }
            });

            // Refresh Lucide Icons
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</x-app-layout>
