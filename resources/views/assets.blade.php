<x-app-layout>

    {{-- ============================================ --}}
    {{-- DATA DUMMY ASSETS (Frontend Prototype)        --}}
    {{-- ============================================ --}}
    @php
        $assets = $assets ?? [
            [
                'id' => 'AST-001',
                'name' => 'ONT ZTE F670L',
                'category' => 'Modem/ONT',
                'serial' => 'ZTE001234',
                'location' => 'Gudang Utama',
                'status' => 'Aktif',
                'vendor' => 'ZTE',
                'purchase_date' => '12 Januari 2024',
                'warranty' => '12 Bulan',
                'description' => 'ONT ZTE F670L untuk layanan internet dengan kecepatan tinggi dan stabil.',
                'specs' => 'GPON, 4x Gigabit Ethernet, 2x POTS, Dual-Band Wi-Fi 2.4/5GHz',
                'icon' => 'router',
                'history' => [
                    ['title' => 'Asset didaftarkan', 'date' => '12 Jan 2024 10:00', 'desc' => 'Penerimaan barang dari vendor ZTE.'],
                    ['title' => 'Status asset diperbarui', 'date' => '15 Mar 2024 14:30', 'desc' => 'Pengujian kualitas fungsi operasional selesai.'],
                    ['title' => 'Asset menjalani maintenance', 'date' => '08 Okt 2026 09:15', 'desc' => 'Pemeriksaan berkala firmware dan kestabilan transmisi optik.'],
                ],
                'related_tickets' => 3,
                'related_devices' => 1,
                'docs' => [
                    ['name' => 'Manual Book ONT ZTE F670L.pdf', 'size' => '2.4 MB'],
                    ['name' => 'Faktur & Kartu Garansi Resmi.pdf', 'size' => '1.1 MB'],
                ],
            ],
            [
                'id' => 'AST-002',
                'name' => 'ONT Huawei HG8245H',
                'category' => 'Modem/ONT',
                'serial' => 'HUA001567',
                'location' => 'Gudang Situbondo',
                'status' => 'Aktif',
                'vendor' => 'Huawei',
                'purchase_date' => '15 Februari 2024',
                'warranty' => '12 Bulan',
                'description' => 'EchoLife HG8245H GPON terminal pelanggan dengan keandalan transmisi tinggi.',
                'specs' => '4 GE ports, 2 POTS ports, 1 USB port, Wi-Fi 802.11b/g/n',
                'icon' => 'router',
                'history' => [
                    ['title' => 'Asset didaftarkan', 'date' => '15 Feb 2024 11:20', 'desc' => 'Diterima di Gudang Situbondo.'],
                    ['title' => 'Pemeriksaan Awal', 'date' => '16 Feb 2024 09:00', 'desc' => 'Lolos QC kelayakan perangkat.'],
                ],
                'related_tickets' => 1,
                'related_devices' => 0,
                'docs' => [
                    ['name' => 'Panduan Teknis HG8245H.pdf', 'size' => '1.8 MB'],
                ],
            ],
            [
                'id' => 'AST-003',
                'name' => 'Router Mikrotik',
                'category' => 'Perangkat Jaringan',
                'serial' => 'MTK001890',
                'location' => 'Gudang Utama',
                'status' => 'Aktif',
                'vendor' => 'Mikrotik',
                'purchase_date' => '10 Januari 2024',
                'warranty' => '24 Bulan',
                'description' => 'RouterBOARD hEX RB750Gr3 untuk manajemen bandwidth dan routing core cabang.',
                'specs' => 'Dual Core 880MHz, 256MB RAM, 5x Gigabit LAN, RouterOS Level 4',
                'icon' => 'network',
                'history' => [
                    ['title' => 'Asset didaftarkan', 'date' => '10 Jan 2024 09:00', 'desc' => 'Pembelian batch Router kantor.'],
                    ['title' => 'Konfigurasi Baseline', 'date' => '12 Jan 2024 13:00', 'desc' => 'Konfigurasi VLAN & firewall security.'],
                ],
                'related_tickets' => 2,
                'related_devices' => 3,
                'docs' => [
                    ['name' => 'Backup Config Baseline.rsc', 'size' => '85 KB'],
                ],
            ],
            [
                'id' => 'AST-004',
                'name' => 'Switch 24 Port',
                'category' => 'Perangkat Jaringan',
                'serial' => 'SW001234',
                'location' => 'Gudang Besuki',
                'status' => 'Maintenance',
                'vendor' => 'TP-Link',
                'purchase_date' => '20 Maret 2024',
                'warranty' => '12 Bulan',
                'description' => 'Switch Gigabit 24 Port Managed sedang dilakukan pemeriksaan daya port PoE.',
                'specs' => '24x 10/100/1000Mbps RJ45 PoE+, 4x Gigabit SFP Slots',
                'icon' => 'server',
                'history' => [
                    ['title' => 'Asset didaftarkan', 'date' => '20 Mar 2024 14:00', 'desc' => 'Distribusi unit ke Gudang Besuki.'],
                    ['title' => 'Masuk Maintenance', 'date' => '07 Okt 2026 16:20', 'desc' => 'Port 17-20 mengalami penurunan voltase PoE.'],
                ],
                'related_tickets' => 1,
                'related_devices' => 1,
                'docs' => [
                    ['name' => 'Laporan Diagnostik Port.pdf', 'size' => '650 KB'],
                ],
            ],
            [
                'id' => 'AST-005',
                'name' => 'Server Dell R730',
                'category' => 'Server',
                'serial' => 'SVR001567',
                'location' => 'Ruang Server',
                'status' => 'Aktif',
                'vendor' => 'Dell',
                'purchase_date' => '05 Januari 2023',
                'warranty' => '36 Bulan',
                'description' => 'Server rackmount 2U untuk sistem aplikasi operasional dan basis data.',
                'specs' => '2x Intel Xeon E5-2680 v4, 128GB DDR4 ECC, 4x 1.2TB SAS 10K RAID 10',
                'icon' => 'database',
                'history' => [
                    ['title' => 'Server Commissioning', 'date' => '05 Jan 2023 08:30', 'desc' => 'Pemasangan di Rack 02 Data Center.'],
                    ['title' => 'Maintenance Tahunan', 'date' => '10 Jan 2024 22:00', 'desc' => 'Thermal paste renewal dan pembersihan debu.'],
                ],
                'related_tickets' => 0,
                'related_devices' => 4,
                'docs' => [
                    ['name' => 'Dell R730 Deployment Sheet.pdf', 'size' => '3.5 MB'],
                ],
            ],
            [
                'id' => 'AST-006',
                'name' => 'Laptop Technician',
                'category' => 'Laptop',
                'serial' => 'LAP001890',
                'location' => 'Gudang Utama',
                'status' => 'Aktif',
                'vendor' => 'Lenovo',
                'purchase_date' => '12 Mei 2024',
                'warranty' => '24 Bulan',
                'description' => 'Laptop operasional teknisi lapangan untuk commissioning OLT dan analisis sinyal.',
                'specs' => 'Intel Core i5-12450H, 16GB DDR4, 512GB SSD NVMe, RJ45 Gigabit',
                'icon' => 'laptop',
                'history' => [
                    ['title' => 'Serah Terima Inventaris', 'date' => '12 Mei 2024 10:00', 'desc' => 'Diberikan ke tim teknisi lapangan.'],
                ],
                'related_tickets' => 4,
                'related_devices' => 0,
                'docs' => [
                    ['name' => 'Form Tanda Terima Inventaris.pdf', 'size' => '420 KB'],
                ],
            ],
            [
                'id' => 'AST-007',
                'name' => 'Kabel FO 12 Core',
                'category' => 'Kabel',
                'serial' => 'FOO001234',
                'location' => 'Gudang Besuki',
                'status' => 'Aktif',
                'vendor' => 'Fiberhome',
                'purchase_date' => '18 Juni 2024',
                'warranty' => '6 Bulan',
                'description' => 'Kabel fiber optik 12 core outdoor aerial untuk jalur backbone distribusi.',
                'specs' => 'Single Mode G.652D, Loose Tube, Kawat Baja Seling Kuat',
                'icon' => 'cable',
                'history' => [
                    ['title' => 'Penerimaan Drum Kabel', 'date' => '18 Jun 2024 11:00', 'desc' => 'Panjang 2000 meter utuh dalam roll kayu.'],
                ],
                'related_tickets' => 0,
                'related_devices' => 2,
                'docs' => [
                    ['name' => 'Hasil Uji OTDR Spool.pdf', 'size' => '980 KB'],
                ],
            ],
            [
                'id' => 'AST-008',
                'name' => 'ODP Box',
                'category' => 'Perangkat FO',
                'serial' => 'ODP001567',
                'location' => 'Lokasi Lapangan',
                'status' => 'Rusak',
                'vendor' => 'Fiberstar',
                'purchase_date' => '08 Agustus 2023',
                'warranty' => 'Expired',
                'description' => 'Box ODP pole-mount retak fisik akibat tertimpa dahan pohon, dalam antrean penggantian.',
                'specs' => '16 Core Optical Distribution Box, IP65 Waterproof Lock',
                'icon' => 'box',
                'history' => [
                    ['title' => 'Pemasangan Tiang', 'date' => '10 Agu 2023 15:00', 'desc' => 'Dipasang di Tiang Cluster Besuki.'],
                    ['title' => 'Laporan Kerusakan', 'date' => '08 Okt 2026 08:30', 'desc' => 'Dilaporkan rusak fisik oleh tim patroli.'],
                ],
                'related_tickets' => 2,
                'related_devices' => 1,
                'docs' => [
                    ['name' => 'Foto Kerusakan Lapangan.jpg', 'size' => '1.5 MB'],
                ],
            ],
            [
                'id' => 'AST-009',
                'name' => 'Tool Kit',
                'category' => 'Peralatan',
                'serial' => 'TKT001890',
                'location' => 'Gudang Utama',
                'status' => 'Aktif',
                'vendor' => 'Jonard Tools',
                'purchase_date' => '22 Februari 2024',
                'warranty' => '12 Bulan',
                'description' => 'Satu set toolkit penyambungan fiber optik, cleaver presisi, dan visual fault locator.',
                'specs' => 'Optical Power Meter, VFL 10mW, Stripper CFS-2, Precision Cleaver',
                'icon' => 'wrench',
                'history' => [
                    ['title' => 'Registrasi Alat', 'date' => '22 Feb 2024 09:30', 'desc' => 'Kelengkapan mata pisau dan baterai verified.'],
                ],
                'related_tickets' => 5,
                'related_devices' => 0,
                'docs' => [
                    ['name' => 'Sertifikat Kalibrasi OPM.pdf', 'size' => '510 KB'],
                ],
            ],
            [
                'id' => 'AST-010',
                'name' => 'Kamera CCTV',
                'category' => 'Keamanan',
                'serial' => 'CCTV001234',
                'location' => 'Kantor',
                'status' => 'Aktif',
                'vendor' => 'Hikvision',
                'purchase_date' => '11 November 2023',
                'warranty' => '24 Bulan',
                'description' => 'Kamera IP surveillance outdoor 4MP untuk pengawasan keamanan pintu masuk gudang.',
                'specs' => '4MP IP Bullet Camera, 30m IR Night Vision, PoE, IP67 Weatherproof',
                'icon' => 'video',
                'history' => [
                    ['title' => 'Instalasi CCTV', 'date' => '11 Nov 2023 16:00', 'desc' => 'Terkoneksi ke NVR Ruang Security.'],
                ],
                'related_tickets' => 0,
                'related_devices' => 1,
                'docs' => [
                    ['name' => 'Diagram Koneksi NVR.pdf', 'size' => '730 KB'],
                ],
            ],
        ];

        $summary = $summary ?? [
            'total_asset' => 1284,
            'active'      => 1167,
            'maintenance' => 72,
            'damaged'     => 45,
        ];

        $categories = $categories ?? [
            'Modem/ONT',
            'Perangkat Jaringan',
            'Server',
            'Laptop',
            'Kabel',
            'Perangkat FO',
            'Peralatan',
            'Keamanan',
        ];

        $statuses = [
            'Aktif',
            'Maintenance',
            'Rusak',
            'Nonaktif',
        ];

        $assetsById = collect($assets)->keyBy('id')->all();
    @endphp

    <div class="space-y-6">

        {{-- ============================================ --}}
        {{-- 1. HEADER ASSETS                             --}}
        {{-- ============================================ --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-dark tracking-tight">Assets</h1>
                <p class="text-secondary text-sm mt-0.5">Kelola data aset perangkat dan peralatan</p>
            </div>
            <div>
                <button type="button" id="btn-open-create-modal"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-white text-sm font-medium hover:bg-blue-700 shadow-sm transition-colors">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Tambah Asset</span>
                </button>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- 2. SUMMARY CARDS                             --}}
        {{-- ============================================ --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Total Asset --}}
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i data-lucide="monitor" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Total Asset</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">{{ number_format($summary['total_asset']) }}</p>
                </div>
            </div>

            {{-- Aktif --}}
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Aktif</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">{{ number_format($summary['active']) }}</p>
                </div>
            </div>

            {{-- Maintenance --}}
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <i data-lucide="wrench" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Maintenance</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">{{ $summary['maintenance'] }}</p>
                </div>
            </div>

            {{-- Rusak --}}
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Rusak</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">{{ number_format($summary['broken']) }}</p>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- 3. LAYOUT UTAMA (TABEL + PANEL DETAIL)       --}}
        {{-- ============================================ --}}
        <div class="flex flex-col xl:flex-row items-start gap-5">

            {{-- ======================================== --}}
            {{-- KOLOM KIRI: TABEL ASSET                  --}}
            {{-- ======================================== --}}
            <div id="table-container" class="w-full xl:flex-1 bg-white rounded-2xl border border-border shadow-[0_2px_6px_rgba(0,0,0,0.02)] overflow-hidden transition-all duration-300">

                {{-- Toolbar Filter & Search --}}
                <div class="p-4 border-b border-border space-y-3">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="relative flex-1 min-w-[220px]">
                            <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                            <input id="asset-search" type="text"
                                   placeholder="Cari asset, serial number, atau kategori..."
                                   class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-border bg-surface focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        </div>

                        <button type="button" id="btn-toggle-filters"
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-border text-xs font-medium text-dark hover:bg-gray-50 transition-colors">
                            <i data-lucide="filter" class="w-3.5 h-3.5 text-secondary"></i>
                            <span>Filter</span>
                            <i data-lucide="chevron-down" class="w-3 h-3 text-gray-400"></i>
                        </button>

                        <select id="asset-sort"
                                class="text-xs rounded-xl border border-border bg-white px-3 py-2 text-dark focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <option value="code_asc">Kode Asset (A-Z)</option>
                            <option value="code_desc">Kode Asset (Z-A)</option>
                            <option value="name_asc">Nama Asset (A-Z)</option>
                            <option value="status_asc">Status</option>
                        </select>
                    </div>

                    {{-- Dropdown Panel Filter Tambahan --}}
                    <div id="filter-dropdown-panel" class="hidden grid-cols-1 sm:grid-cols-3 gap-2.5 pt-2 border-t border-gray-100">
                        <select id="filter-category" class="text-xs rounded-lg border border-border bg-white px-2.5 py-1.5 text-dark">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>

                        <select id="filter-status" class="text-xs rounded-lg border border-border bg-white px-2.5 py-1.5 text-dark">
                            <option value="">Semua Status</option>
                            @foreach ($statuses as $st)
                                <option value="{{ $st }}">{{ $st }}</option>
                            @endforeach
                        </select>

                        <button type="button" id="btn-reset-filters"
                                class="text-xs rounded-lg border border-border px-3 py-1.5 text-secondary hover:bg-gray-50 text-center font-medium">
                            Reset Filter
                        </button>
                    </div>
                </div>

                {{-- Tabel Data Asset --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr class="text-secondary bg-[#FAFCFF] border-b border-border font-medium text-[11px] uppercase tracking-wider">
                                <th class="pl-4 py-3 w-8">
                                    <input id="check-all" type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary/20">
                                </th>
                                <th class="py-3 pr-3">Kode Asset</th>
                                <th class="py-3 pr-3">Nama Asset</th>
                                <th class="py-3 pr-3">Kategori</th>
                                <th class="py-3 pr-3">Serial Number</th>
                                <th class="py-3 pr-3">Lokasi</th>
                                <th class="py-3 pr-3 text-center">Status</th>
                                <th class="py-3 pr-4 text-center w-8">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="asset-table-body" class="divide-y divide-gray-100">
                            @foreach ($assets as $idx => $item)
                                @php
                                    $statusBadgeClass = match($item['status']) {
                                        'Aktif'       => 'bg-emerald-50 text-emerald-600 border border-emerald-200/60',
                                        'Maintenance' => 'bg-amber-50 text-amber-600 border border-amber-200/60 font-semibold',
                                        'Rusak'       => 'bg-red-50 text-red-600 border border-red-200/60 font-bold',
                                        default       => 'bg-slate-100 text-slate-500 border border-slate-200/60',
                                    };
                                @endphp
                                <tr class="asset-row cursor-pointer transition-colors hover:bg-blue-50/40 {{ $idx === 0 ? 'bg-blue-50/70 border-l-4 border-l-primary' : '' }}"
                                    data-id="{{ $item['id'] }}"
                                    data-name="{{ $item['name'] }}"
                                    data-category="{{ $item['category'] }}"
                                    data-serial="{{ $item['serial'] }}"
                                    data-location="{{ $item['location'] }}"
                                    data-status="{{ $item['status'] }}"
                                    data-search="{{ strtolower($item['id'] . ' ' . $item['name'] . ' ' . $item['category'] . ' ' . $item['serial'] . ' ' . $item['location']) }}">
                                    <td class="pl-4 py-3">
                                        <input type="checkbox" class="row-checkbox rounded border-gray-300 text-primary focus:ring-primary/20">
                                    </td>
                                    <td class="py-3 pr-3 font-semibold text-primary whitespace-nowrap">
                                        {{ $item['id'] }}
                                    </td>
                                    <td class="py-3 pr-3">
                                        <p class="font-medium text-dark">{{ $item['name'] }}</p>
                                        <p class="text-[11px] text-secondary">{{ $item['vendor'] }}</p>
                                    </td>
                                    <td class="py-3 pr-3 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-blue-50 text-blue-600 border border-blue-200/50">
                                            {{ $item['category'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 pr-3 whitespace-nowrap font-mono text-dark">
                                        {{ $item['serial'] }}
                                    </td>
                                    <td class="py-3 pr-3 whitespace-nowrap text-secondary">
                                        {{ $item['location'] }}
                                    </td>
                                    <td class="py-3 pr-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium whitespace-nowrap {{ $statusBadgeClass }}">
                                            {{ $item['status'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 pr-4 text-center">
                                        <button type="button" class="p-1 rounded-md text-gray-400 hover:text-dark hover:bg-gray-100" data-dummy-action="Menu Aksi {{ $item['id'] }}">
                                            <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                            <tr id="empty-state-row" class="hidden">
                                <td colspan="8" class="py-12 text-center text-gray-400">
                                    <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                                    Tidak ada aset yang cocok dengan kriteria pencarian / filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Dummy --}}
                <div class="p-3.5 border-t border-border flex flex-wrap items-center justify-between gap-3 text-xs">
                    <p id="table-info" class="text-secondary">Menampilkan 1-10 dari {{ count($assets) }} aset</p>
                    <div class="flex items-center gap-1">
                        <button type="button" class="w-7 h-7 rounded-lg border border-border flex items-center justify-center text-secondary hover:bg-gray-50" data-dummy-action="Halaman Sebelumnya">
                            <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
                        </button>
                        <button type="button" class="w-7 h-7 rounded-lg bg-primary text-white font-medium flex items-center justify-center">1</button>
                        <button type="button" class="w-7 h-7 rounded-lg border border-border text-secondary hover:bg-gray-50 flex items-center justify-center" data-dummy-action="Halaman 2">2</button>
                        <button type="button" class="w-7 h-7 rounded-lg border border-border text-secondary hover:bg-gray-50 flex items-center justify-center" data-dummy-action="Halaman 3">3</button>
                        <span class="px-1 text-gray-400">...</span>
                        <button type="button" class="w-7 h-7 rounded-lg border border-border text-secondary hover:bg-gray-50 flex items-center justify-center" data-dummy-action="Halaman 128">128</button>
                        <button type="button" class="w-7 h-7 rounded-lg border border-border flex items-center justify-center text-secondary hover:bg-gray-50" data-dummy-action="Halaman Berikutnya">
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>

            </div>

            {{-- ======================================== --}}
            {{-- KOLOM KANAN: PANEL DETAIL ASSET          --}}
            {{-- ======================================== --}}
            <aside id="asset-detail-panel"
                   class="w-full xl:w-[480px] 2xl:w-[520px] bg-white rounded-2xl border border-border shadow-[0_2px_6px_rgba(0,0,0,0.02)] p-5 shrink-0 transition-all duration-300 xl:sticky xl:top-20">

                {{-- Header Detail Panel --}}
                <div class="flex items-start justify-between pb-4 border-b border-border">
                    <div class="flex items-start gap-3.5">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-primary flex items-center justify-center shrink-0 border border-blue-200/50">
                            <i data-lucide="router" id="detail-asset-icon" class="w-6 h-6"></i>
                        </div>
                        <div class="space-y-0.5">
                            <div class="flex items-center gap-2">
                                <span id="detail-asset-id" class="text-lg font-bold text-primary">AST-001</span>
                                <span id="detail-status-badge" class="px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-600 border border-emerald-200/60">Aktif</span>
                            </div>
                            <h2 id="detail-asset-name" class="text-base font-bold text-dark">ONT ZTE F670L</h2>
                            <p class="text-xs text-secondary flex items-center gap-1.5">
                                <span id="detail-asset-category">Modem/ONT</span>
                                <span>•</span>
                                <span class="font-mono text-dark" id="detail-asset-serial">ZTE001234</span>
                            </p>
                        </div>
                    </div>

                    <button type="button" id="btn-close-detail"
                            class="p-1.5 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100 transition-colors"
                            title="Tutup Panel Detail">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                {{-- Kartu Metrik Ringkas 4 Kolom --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 p-3 rounded-xl bg-surface border border-border/80 text-xs my-4">
                    <div>
                        <p class="text-[10px] text-secondary uppercase font-semibold">Vendor</p>
                        <p id="detail-card-vendor" class="text-xs font-bold text-dark mt-0.5 truncate">ZTE</p>
                        <p class="text-[10px] text-secondary">Penyedia</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-secondary uppercase font-semibold">Garansi</p>
                        <p id="detail-card-warranty" class="text-xs font-bold text-dark mt-0.5">12 Bulan</p>
                        <p class="text-[10px] text-emerald-600 font-medium">Valid</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-secondary uppercase font-semibold">Lokasi</p>
                        <p id="detail-card-location" class="font-bold text-dark mt-0.5 truncate">Gudang Utama</p>
                        <p class="text-[10px] text-secondary">Penyimpanan</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-secondary uppercase font-semibold">Status Aset</p>
                        <p id="detail-card-status" class="font-bold text-emerald-600 mt-0.5">Aktif</p>
                        <p class="text-[10px] text-secondary">Operasional</p>
                    </div>
                </div>

                {{-- 4 Tab Navigation --}}
                <div class="flex border-b border-border text-xs font-semibold mb-4 gap-6" id="panel-tabs">
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-primary text-primary transition-colors" data-target="tab-detail">Detail</button>
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-transparent text-secondary hover:text-dark transition-colors" data-target="tab-history">Riwayat</button>
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-transparent text-secondary hover:text-dark transition-colors" data-target="tab-related">Terkait</button>
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-transparent text-secondary hover:text-dark transition-colors" data-target="tab-docs">Dokumentasi</button>
                </div>

                {{-- Konten Tab Panel --}}
                <div class="space-y-4">

                    {{-- TAB 1: DETAIL --}}
                    <div id="tab-detail" class="tab-pane space-y-3 text-xs">
                        <div class="space-y-2">
                            <div>
                                <span class="text-secondary text-[11px] block">Deskripsi:</span>
                                <p id="info-description" class="text-dark bg-surface p-2.5 rounded-xl border border-border mt-1 leading-relaxed">
                                    ONT ZTE F670L untuk layanan internet dengan kecepatan tinggi dan stabil.
                                </p>
                            </div>
                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div>
                                    <span class="text-secondary text-[11px] block">Kode Asset:</span>
                                    <span id="info-code" class="font-semibold text-dark">AST-001</span>
                                </div>
                                <div>
                                    <span class="text-secondary text-[11px] block">Serial Number:</span>
                                    <span id="info-serial" class="font-mono font-semibold text-dark">ZTE001234</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div>
                                    <span class="text-secondary text-[11px] block">Kategori:</span>
                                    <span id="info-category" class="font-medium text-dark">Modem/ONT</span>
                                </div>
                                <div>
                                    <span class="text-secondary text-[11px] block">Vendor / Pabrikan:</span>
                                    <span id="info-vendor" class="font-medium text-dark">ZTE</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div>
                                    <span class="text-secondary text-[11px] block">Tanggal Pembelian:</span>
                                    <span id="info-purchase" class="font-medium text-dark">12 Januari 2024</span>
                                </div>
                                <div>
                                    <span class="text-secondary text-[11px] block">Masa Garansi:</span>
                                    <span id="info-warranty" class="font-medium text-dark">12 Bulan</span>
                                </div>
                            </div>
                            <div>
                                <span class="text-secondary text-[11px] block">Spesifikasi Teknis:</span>
                                <p id="info-specs" class="text-secondary bg-surface p-2.5 rounded-xl border border-border mt-1 font-mono text-[11px]">
                                    GPON, 4x Gigabit Ethernet, 2x POTS, Dual-Band Wi-Fi 2.4/5GHz
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 2: RIWAYAT --}}
                    <div id="tab-history" class="tab-pane hidden space-y-3 text-xs">
                        <p class="font-semibold text-dark text-xs mb-2">Riwayat & Timeline Aset:</p>
                        <div id="history-timeline" class="space-y-3 relative before:absolute before:inset-0 before:left-2 before:w-0.5 before:bg-gray-200">
                            {{-- Diisi secara dinamis oleh JavaScript --}}
                        </div>
                    </div>

                    {{-- TAB 3: TERKAIT --}}
                    <div id="tab-related" class="tab-pane hidden space-y-3 text-xs">
                        <p class="font-semibold text-dark text-xs mb-2">Hubungan Tiket & Infrastruktur Jaringan:</p>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3 rounded-xl bg-surface border border-border">
                                <p class="text-[11px] text-secondary">Tiket Terhubung</p>
                                <p id="related-tickets-count" class="text-xl font-bold text-dark mt-1">3 tiket</p>
                                <a href="{{ route('tickets.index') }}" class="text-[11px] text-primary font-medium hover:underline mt-1 block">Buka Halaman Ticket →</a>
                            </div>
                            <div class="p-3 rounded-xl bg-surface border border-border">
                                <p class="text-[11px] text-secondary">Perangkat Jaringan Terkait</p>
                                <p id="related-devices-count" class="text-xl font-bold text-dark mt-1">1 perangkat</p>
                                <a href="#" class="text-[11px] text-primary font-medium hover:underline mt-1 block" data-dummy-action="Lihat Topologi Jaringan">Lihat di Topologi →</a>
                            </div>
                        </div>
                        <div class="p-3 rounded-xl bg-blue-50/50 border border-blue-200/40 text-[11px] text-secondary flex items-start gap-2 mt-2">
                            <i data-lucide="info" class="w-4 h-4 text-primary shrink-0 mt-0.5"></i>
                            <span id="related-summary-text">Asset ini terhubung dengan 3 ticket dan 1 perangkat jaringan.</span>
                        </div>
                    </div>

                    {{-- TAB 4: DOKUMENTASI --}}
                    <div id="tab-docs" class="tab-pane hidden space-y-2.5 text-xs">
                        <p class="font-semibold text-dark text-xs mb-2">Dokumentasi & Berkas Lampiran:</p>
                        <div id="docs-list" class="space-y-2">
                            {{-- Diisi dinamis oleh JavaScript --}}
                        </div>
                    </div>

                    {{-- Status & Aksi --}}
                    <div class="p-3.5 rounded-xl border border-border bg-surface space-y-3">
                        <p class="font-semibold text-dark text-xs">Status & Aksi</p>
                        <select id="action-status-select" class="w-full text-xs rounded-lg border border-border bg-white px-3 py-2 text-dark focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <option value="Aktif">Aktif</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Rusak">Rusak</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" id="btn-trigger-edit" class="py-2 px-3 rounded-lg bg-primary text-white text-xs font-semibold hover:bg-blue-700 shadow-sm transition-colors text-center">
                                Edit Asset
                            </button>
                            <button type="button" id="btn-trigger-maintenance" class="py-2 px-3 rounded-lg bg-white border border-border text-dark text-xs font-medium hover:bg-gray-50 transition-colors text-center">
                                Maintenance
                            </button>
                            <button type="button" id="btn-trigger-exchange" class="py-2 px-3 rounded-lg bg-white border border-border text-dark text-xs font-medium hover:bg-gray-50 transition-colors text-center">
                                Tukar Asset
                            </button>
                            <button type="button" id="btn-trigger-delete" class="py-2 px-3 rounded-lg bg-white border border-red-200 text-danger text-xs font-medium hover:bg-red-50 transition-colors text-center">
                                Hapus Asset
                            </button>
                        </div>
                    </div>

                    {{-- Quick Info --}}
                    <div class="p-3.5 rounded-xl border border-border bg-surface space-y-2.5 text-xs">
                        <p class="font-semibold text-dark text-xs">Quick Info</p>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-secondary text-[11px]">Lokasi:</p>
                                <p id="quick-location" class="font-medium text-dark">Gudang Utama</p>
                            </div>
                            <a href="#" class="text-primary text-[11px] font-semibold hover:underline" data-dummy-action="Detail Lokasi Gudang">Lihat Detail</a>
                        </div>
                        <div class="flex items-center justify-between border-t border-gray-200/60 pt-2">
                            <div>
                                <p class="text-secondary text-[11px]">Vendor:</p>
                                <p id="quick-vendor" class="font-medium text-dark">ZTE</p>
                            </div>
                            <a href="#" class="text-primary text-[11px] font-semibold hover:underline" data-dummy-action="Detail Vendor ZTE">Lihat Detail</a>
                        </div>
                        <div class="border-t border-gray-200/60 pt-2 text-[11px] text-secondary">
                            <p id="quick-connected-note">Asset ini terhubung dengan 3 ticket dan 1 perangkat jaringan.</p>
                        </div>
                    </div>

                </div>

            </aside>

        </div>

    </div>

    {{-- ============================================ --}}
    {{-- 4. MODAL TAMBAH ASSET                        --}}
    {{-- ============================================ --}}
    <div id="modal-create-asset" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs hidden">
        <div class="bg-white rounded-2xl border border-border shadow-2xl max-w-lg w-full overflow-hidden animate-in fade-in duration-150">
            <div class="p-4 border-b border-border flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-dark">Tambah Asset Baru</h3>
                    <p class="text-secondary text-xs">Registrasi master aset dan inventaris perangkat</p>
                </div>
                <button type="button" id="btn-close-create-modal" class="p-1 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form id="form-create-asset" class="p-5 space-y-3.5 text-xs">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1">Kode Asset <span class="text-danger">*</span></label>
                        <input type="text" id="create-code" placeholder="AST-011" required
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Serial Number <span class="text-danger">*</span></label>
                        <input type="text" id="create-serial" placeholder="SN12345678" required
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-dark mb-1">Nama Asset <span class="text-danger">*</span></label>
                    <input type="text" id="create-name" placeholder="Contoh: Switch Cisco Catalyst 2960" required
                           class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1">Kategori <span class="text-danger">*</span></label>
                        <select id="create-category" class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Lokasi Penyimpanan <span class="text-danger">*</span></label>
                        <input type="text" id="create-location" placeholder="Gudang Utama" required
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1">Vendor</label>
                        <input type="text" id="create-vendor" placeholder="Cisco / ZTE"
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Status Awal</label>
                        <select id="create-status" class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                            <option value="Aktif">Aktif</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Rusak">Rusak</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-dark mb-1">Deskripsi & Catatan</label>
                    <textarea id="create-desc" rows="2.5" placeholder="Keterangan peruntukan atau spesifikasi aset..."
                              class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
                </div>

                <div class="pt-3 border-t border-border flex items-center justify-end gap-2">
                    <button type="button" id="btn-cancel-create-modal" class="px-3.5 py-2 rounded-xl border border-border text-secondary hover:bg-gray-50 font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-blue-700 shadow-sm">Simpan Asset</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- 5. MODAL EDIT ASSET                          --}}
    {{-- ============================================ --}}
    <div id="modal-edit-asset" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs hidden">
        <div class="bg-white rounded-2xl border border-border shadow-2xl max-w-lg w-full overflow-hidden animate-in fade-in duration-150">
            <div class="p-4 border-b border-border flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-dark">Edit Data Asset</h3>
                    <p class="text-secondary text-xs">Perbarui rincian dan spesifikasi aset</p>
                </div>
                <button type="button" id="btn-close-edit-modal" class="p-1 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form id="form-edit-asset" class="p-5 space-y-3.5 text-xs">
                <input type="hidden" id="edit-id-hidden">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1">Kode Asset</label>
                        <input type="text" id="edit-code" readonly
                               class="w-full rounded-xl border border-border bg-gray-50 text-gray-500 px-3 py-2 text-xs cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Serial Number</label>
                        <input type="text" id="edit-serial" required
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-dark mb-1">Nama Asset</label>
                    <input type="text" id="edit-name" required
                           class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1">Kategori</label>
                        <select id="edit-category" class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Lokasi</label>
                        <input type="text" id="edit-location" required
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1">Vendor</label>
                        <input type="text" id="edit-vendor"
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Status</label>
                        <select id="edit-status" class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                            <option value="Aktif">Aktif</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Rusak">Rusak</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-dark mb-1">Deskripsi</label>
                    <textarea id="edit-desc" rows="2.5"
                              class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
                </div>

                <div class="pt-3 border-t border-border flex items-center justify-end gap-2">
                    <button type="button" id="btn-cancel-edit-modal" class="px-3.5 py-2 rounded-xl border border-border text-secondary hover:bg-gray-50 font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-blue-700 shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- 6. MODAL MAINTENANCE ASSET                   --}}
    {{-- ============================================ --}}
    <div id="modal-maintenance" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs hidden">
        <div class="bg-white rounded-2xl border border-border shadow-2xl max-w-md w-full overflow-hidden animate-in fade-in duration-150">
            <div class="p-4 border-b border-border flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-dark">Pencatatan Maintenance</h3>
                    <p class="text-secondary text-xs">Jadwalkan atau catat pemeliharaan aset</p>
                </div>
                <button type="button" id="btn-close-maint-modal" class="p-1 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form id="form-maintenance" class="p-5 space-y-3.5 text-xs">
                <div>
                    <label class="block font-medium text-dark mb-1">Aset yang Dipelihara</label>
                    <input type="text" id="maint-asset-name" readonly
                           class="w-full rounded-xl border border-border bg-gray-50 text-gray-600 px-3 py-2 text-xs">
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1">Jenis Pekerjaan Maintenance</label>
                    <select class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                        <option value="Pemeriksaan Berkala">Pemeriksaan Berkala (Preventive)</option>
                        <option value="Perbaikan Kerusakan">Perbaikan Kerusakan (Corrective)</option>
                        <option value="Upgrade Firmware">Upgrade Firmware / Konfigurasi</option>
                        <option value="Pembersihan Fisik">Pembersihan Fisik Perangkat</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1">Teknisi Penanggung Jawab</label>
                    <input type="text" placeholder="Nama teknisi (Contoh: Budi / Ahmad)" required
                           class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1">Catatan Tindakan</label>
                    <textarea rows="3" placeholder="Rincian bagian yang diperiksa atau diganti..." required
                              class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20"></textarea>
                </div>
                <div class="pt-3 border-t border-border flex items-center justify-end gap-2">
                    <button type="button" id="btn-cancel-maint-modal" class="px-3.5 py-2 rounded-xl border border-border text-secondary hover:bg-gray-50 font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 text-white font-semibold hover:bg-amber-700 shadow-sm">Simpan Catatan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Toast Notifikasi --}}
    <div id="toast-notification"
         class="fixed bottom-5 right-5 z-50 hidden max-w-sm rounded-xl bg-dark text-white text-xs px-4 py-3 shadow-xl transition-all">
    </div>

    {{-- ============================================ --}}
    {{-- 7. JAVASCRIPT LOGIC INTERAKTIF              --}}
    {{-- ============================================ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Data aset dari Blade
            let assetList = @json($assets);
            let assetMap = {};
            assetList.forEach(a => { assetMap[a.id] = a; });

            let currentSelectedId = 'AST-001';

            // Toast helper
            let toastTimer;
            function showToast(message) {
                const toast = document.getElementById('toast-notification');
                toast.textContent = message;
                toast.classList.remove('hidden');
                clearTimeout(toastTimer);
                toastTimer = setTimeout(() => toast.classList.add('hidden'), 2600);
            }

            // Dummy Action Handler
            document.querySelectorAll('[data-dummy-action]').forEach(el => {
                el.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    showToast(el.getAttribute('data-dummy-action') + ' (Aksi UI)');
                });
            });

            // 1. Search & Filter & Sort
            const searchInput = document.getElementById('asset-search');
            const categoryFilter = document.getElementById('filter-category');
            const statusFilter = document.getElementById('filter-status');
            const sortSelect = document.getElementById('asset-sort');
            const rows = Array.from(document.querySelectorAll('.asset-row'));
            const tbody = document.getElementById('asset-table-body');
            const emptyState = document.getElementById('empty-state-row');
            const tableInfo = document.getElementById('table-info');

            function applyFilters() {
                const q = searchInput.value.toLowerCase().trim();
                const cat = categoryFilter.value;
                const st = statusFilter.value;
                const sortMode = sortSelect.value;

                // Sorting rows
                const sortedRows = rows.slice().sort((a, b) => {
                    if (sortMode === 'code_asc') return a.dataset.id.localeCompare(b.dataset.id);
                    if (sortMode === 'code_desc') return b.dataset.id.localeCompare(a.dataset.id);
                    if (sortMode === 'name_asc') return a.dataset.name.localeCompare(b.dataset.name);
                    if (sortMode === 'status_asc') return a.dataset.status.localeCompare(b.dataset.status);
                    return 0;
                });

                sortedRows.forEach(r => tbody.insertBefore(r, emptyState));

                let visibleCount = 0;
                sortedRows.forEach(r => {
                    const matchQ = !q || r.dataset.search.includes(q);
                    const matchCat = !cat || r.dataset.category === cat;
                    const matchSt = !st || r.dataset.status === st;

                    const match = matchQ && matchCat && matchSt;
                    r.classList.toggle('hidden', !match);
                    if (match) visibleCount++;
                });

                emptyState.classList.toggle('hidden', visibleCount > 0);
                tableInfo.textContent = `Menampilkan ${visibleCount} dari ${rows.length} aset`;
            }

            [searchInput, categoryFilter, statusFilter, sortSelect].forEach(el => {
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
                categoryFilter.value = '';
                statusFilter.value = '';
                sortSelect.value = 'code_asc';
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

            // 2. Populate Detail Panel
            const detailPanel = document.getElementById('asset-detail-panel');

            function populateDetail(assetId) {
                const a = assetMap[assetId];
                if (!a) return;
                currentSelectedId = assetId;

                // Header
                document.getElementById('detail-asset-id').textContent = a.id;
                document.getElementById('detail-asset-name').textContent = a.name;
                document.getElementById('detail-asset-category').textContent = a.category;
                document.getElementById('detail-asset-serial').textContent = a.serial;

                const statusBadge = document.getElementById('detail-status-badge');
                statusBadge.textContent = a.status;
                if (a.status === 'Aktif') {
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-600 border border-emerald-200/60';
                } else if (a.status === 'Maintenance') {
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-600 border border-amber-200/60 font-semibold';
                } else if (a.status === 'Rusak') {
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-red-50 text-red-600 border border-red-200/60 font-bold';
                } else {
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-500 border border-slate-200/60';
                }

                // 4 Kotak Metrik
                document.getElementById('detail-card-vendor').textContent = a.vendor || '-';
                document.getElementById('detail-card-warranty').textContent = a.warranty || '-';
                document.getElementById('detail-card-location').textContent = a.location || 'Gudang Utama';
                const cardStatus = document.getElementById('detail-card-status');
                cardStatus.textContent = a.status;
                cardStatus.className = `font-bold mt-0.5 truncate ${a.status === 'Aktif' ? 'text-emerald-600' : (a.status === 'Maintenance' ? 'text-amber-600' : 'text-danger')}`;

                // Tab Detail
                document.getElementById('info-code').textContent = a.id;
                document.getElementById('info-name').textContent = a.name;
                document.getElementById('info-category').textContent = a.category;
                document.getElementById('info-serial').textContent = a.serial;
                document.getElementById('info-vendor').textContent = a.vendor || '-';
                document.getElementById('info-purchase').textContent = a.purchase_date || '-';
                document.getElementById('info-warranty').textContent = a.warranty || '-';
                document.getElementById('info-specs').textContent = a.specs || '-';
                document.getElementById('info-description').textContent = a.description || 'Tidak ada deskripsi.';

                // Tab Riwayat
                const timeline = document.getElementById('history-timeline');
                const historyData = a.history || [];
                timeline.innerHTML = historyData.length ? historyData.map((h, i) => `
                    <div class="relative flex items-start gap-3 pl-6">
                        <span class="absolute left-1 top-1 w-2.5 h-2.5 rounded-full ${i === 0 ? 'bg-primary ring-4 ring-white' : 'bg-gray-300 ring-4 ring-white'}"></span>
                        <div>
                            <p class="font-semibold text-dark">${h.title}</p>
                            <p class="text-[11px] text-secondary mt-0.5">${h.desc}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">${h.date}</p>
                        </div>
                    </div>
                `).join('') : '<p class="text-secondary text-center py-4">Belum ada riwayat tercatat.</p>';

                // Tab Terkait
                document.getElementById('related-tickets-count').textContent = `${a.related_tickets || 0} tiket`;
                document.getElementById('related-devices-count').textContent = `${a.related_devices || 0} perangkat`;
                const summaryText = `Asset ini terhubung dengan ${a.related_tickets || 0} ticket dan ${a.related_devices || 0} perangkat jaringan.`;
                document.getElementById('related-summary-text').textContent = summaryText;
                document.getElementById('quick-connected-note').textContent = summaryText;

                // Tab Dokumen
                const docsList = document.getElementById('docs-list');
                const docsData = a.docs || [];
                docsList.innerHTML = docsData.length ? docsData.map(d => `
                    <div class="flex items-center justify-between p-2.5 rounded-xl border border-border bg-surface">
                        <div class="flex items-center gap-2">
                            <i data-lucide="file-text" class="w-4 h-4 text-primary"></i>
                            <span class="font-medium text-dark">${d.name}</span>
                        </div>
                        <span class="text-[11px] text-secondary">${d.size}</span>
                    </div>
                `).join('') : '<p class="text-secondary text-center py-4">Belum ada berkas lampiran.</p>';

                // Quick Info
                document.getElementById('quick-location').textContent = a.location || '-';
                document.getElementById('quick-vendor').textContent = a.vendor || '-';
                document.getElementById('action-status-select').value = a.status;

                if (window.lucide) window.lucide.createIcons();
            }

            // Click Row Event
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

            // 3. Tab Switching
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

            // 4. Modal Tambah Asset
            const createModal = document.getElementById('modal-create-asset');
            document.getElementById('btn-open-create-modal').addEventListener('click', () => {
                createModal.classList.remove('hidden');
            });
            const closeCreateModal = () => createModal.classList.add('hidden');
            document.getElementById('btn-close-create-modal').addEventListener('click', closeCreateModal);
            document.getElementById('btn-cancel-create-modal').addEventListener('click', closeCreateModal);

            document.getElementById('form-create-asset').addEventListener('submit', (e) => {
                e.preventDefault();
                const code = document.getElementById('create-code').value.trim();
                const serial = document.getElementById('create-serial').value.trim();
                const name = document.getElementById('create-name').value.trim();
                const category = document.getElementById('create-category').value;
                const location = document.getElementById('create-location').value.trim();
                const vendor = document.getElementById('create-vendor').value.trim() || 'Vendor Umum';
                const status = document.getElementById('create-status').value;
                const desc = document.getElementById('create-desc').value.trim();

                const newAsset = {
                    id: code,
                    name: name,
                    category: category,
                    serial: serial,
                    location: location,
                    status: status,
                    vendor: vendor,
                    purchase_date: 'Hari ini',
                    warranty: '12 Bulan',
                    description: desc || 'Aset baru berhasil didaftarkan.',
                    specs: 'Standard Deployment Specs',
                    icon: 'box',
                    history: [
                        { title: 'Asset Didaftarkan', date: 'Hari ini', desc: 'Registrasi melalui portal NEXOPS.' }
                    ],
                    related_tickets: 0,
                    related_devices: 0,
                    docs: []
                };

                assetMap[code] = newAsset;
                closeCreateModal();
                showToast(`Asset ${code} - ${name} berhasil ditambahkan!`);
                populateDetail(code);
            });

            // 5. Modal Edit Asset
            const editModal = document.getElementById('modal-edit-asset');
            document.getElementById('btn-trigger-edit').addEventListener('click', () => {
                const current = assetMap[currentSelectedId];
                if (!current) return;

                document.getElementById('edit-id-hidden').value = current.id;
                document.getElementById('edit-code').value = current.id;
                document.getElementById('edit-serial').value = current.serial;
                document.getElementById('edit-name').value = current.name;
                document.getElementById('edit-category').value = current.category;
                document.getElementById('edit-location').value = current.location;
                document.getElementById('edit-vendor').value = current.vendor || '';
                document.getElementById('edit-status').value = current.status;
                document.getElementById('edit-desc').value = current.description || '';

                editModal.classList.remove('hidden');
            });

            const closeEditModal = () => editModal.classList.add('hidden');
            document.getElementById('btn-close-edit-modal').addEventListener('click', closeEditModal);
            document.getElementById('btn-cancel-edit-modal').addEventListener('click', closeEditModal);

            document.getElementById('form-edit-asset').addEventListener('submit', (e) => {
                e.preventDefault();
                const code = document.getElementById('edit-id-hidden').value;
                const current = assetMap[code];
                if (!current) return;

                current.name = document.getElementById('edit-name').value.trim();
                current.serial = document.getElementById('edit-serial').value.trim();
                current.category = document.getElementById('edit-category').value;
                current.location = document.getElementById('edit-location').value.trim();
                current.vendor = document.getElementById('edit-vendor').value.trim();
                current.status = document.getElementById('edit-status').value;
                current.description = document.getElementById('edit-desc').value.trim();

                closeEditModal();
                showToast(`Perubahan asset ${code} berhasil disimpan!`);
                populateDetail(code);

                // Update table row
                const row = document.querySelector(`.asset-row[data-id="${code}"]`);
                if (row) {
                    row.querySelector('td:nth-child(3) p:first-child').textContent = current.name;
                    row.querySelector('td:nth-child(3) p:last-child').textContent = current.vendor;
                    row.querySelector('td:nth-child(4) span').textContent = current.category;
                    row.querySelector('td:nth-child(5)').textContent = current.serial;
                    row.querySelector('td:nth-child(6)').textContent = current.location;
                    row.dataset.name = current.name;
                    row.dataset.category = current.category;
                    row.dataset.serial = current.serial;
                    row.dataset.location = current.location;
                    row.dataset.status = current.status;
                }
            });

            // 6. Modal Maintenance
            const maintModal = document.getElementById('modal-maintenance');
            document.getElementById('btn-trigger-maintenance').addEventListener('click', () => {
                const current = assetMap[currentSelectedId];
                if (!current) return;
                document.getElementById('maint-asset-name').value = `${current.name} (${current.id}) - SN: ${current.serial}`;
                maintModal.classList.remove('hidden');
            });
            const closeMaintModal = () => maintModal.classList.add('hidden');
            document.getElementById('btn-close-maint-modal').addEventListener('click', closeMaintModal);
            document.getElementById('btn-cancel-maint-modal').addEventListener('click', closeMaintModal);

            document.getElementById('form-maintenance').addEventListener('submit', (e) => {
                e.preventDefault();
                closeMaintModal();
                showToast('Catatan maintenance berhasil direkam!');
            });

            // 7. Tukar Asset (Exchange)
            document.getElementById('btn-trigger-exchange').addEventListener('click', () => {
                const current = assetMap[currentSelectedId];
                if (!current) return;
                if (confirm(`Apakah Anda ingin memulai proses Tukar Asset untuk "${current.name}" (${current.id})?`)) {
                    showToast(`Proses pergantian unit untuk ${current.id} telah dicatat.`);
                }
            });

            // 8. Hapus Asset
            document.getElementById('btn-trigger-delete').addEventListener('click', () => {
                const current = assetMap[currentSelectedId];
                if (!current) return;

                if (confirm(`Apakah Anda yakin ingin menghapus aset "${current.name}" (${current.id})? Tindakan ini bersifat permanen.`)) {
                    showToast(`Asset ${current.id} berhasil dihapus!`);
                    const row = document.querySelector(`.asset-row[data-id="${current.id}"]`);
                    if (row) row.remove();
                    delete assetMap[current.id];

                    const firstRemaining = document.querySelector('.asset-row');
                    if (firstRemaining) {
                        firstRemaining.click();
                    } else {
                        detailPanel.classList.add('hidden');
                    }
                }
            });

            // Inisialisasi awal produk pertama
            populateDetail('AST-001');

            // Refresh icons
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</x-app-layout>
