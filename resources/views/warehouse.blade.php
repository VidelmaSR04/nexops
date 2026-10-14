<x-app-layout>

    {{-- ============================================ --}}
    {{-- DATA DUMMY WAREHOUSE (Frontend Prototype)     --}}
    {{-- ============================================ --}}
    @php
        $warehouses = $warehouses ?? [
            [
                'id' => 'WH-001',
                'name' => 'Gudang Utama',
                'location' => 'Jl. Raya Situbondo No. 12',
                'area' => 'Situbondo',
                'capacity' => 50000,
                'available_stock' => 42350,
                'used_stock' => 7650,
                'status' => 'Aktif',
                'manager' => 'Budi Santoso',
                'manager_role' => 'Warehouse Staff',
                'categories_count' => 4,
                'description' => 'Gudang penyimpanan utama untuk perangkat pasif/aktif dan material operasional lapangan.',
                'stock_items' => [
                    ['name' => 'ONT ZTE F670L', 'category' => 'Modem/ONT', 'qty' => 70, 'unit' => 'Unit'],
                    ['name' => 'ONT Huawei HG8245H', 'category' => 'Modem/ONT', 'qty' => 30, 'unit' => 'Unit'],
                    ['name' => 'RJ45 Connector', 'category' => 'Accessories', 'qty' => 300, 'unit' => 'Pack'],
                    ['name' => 'Splitter 1:8 PLC', 'category' => 'Perangkat FO', 'qty' => 140, 'unit' => 'Pcs'],
                    ['name' => 'Kabel Dropcore 1C', 'category' => 'Kabel', 'qty' => 90, 'unit' => 'Roll'],
                ],
                'history' => [
                    ['title' => 'Penerimaan PO-2026-089', 'date' => '08 Okt 2026 09:30', 'desc' => 'Masuk 50 unit ONT ZTE F670L dari supplier pusat.'],
                    ['title' => 'Pengambilan Material', 'date' => '08 Okt 2026 08:55', 'desc' => 'Teknisi Budi mengambil 1 unit ONT untuk Ticket #TK-1025.'],
                    ['title' => 'Stock Opname Bulanan', 'date' => '01 Okt 2026 17:00', 'desc' => 'Pemeriksaan fisik 100% klop dengan sistem pencatatan.'],
                ],
                'docs' => [
                    ['name' => 'SOP Pergudangan Standar NEXOPS.pdf', 'size' => '1.8 MB'],
                    ['name' => 'Layout Denah Rak Gudang Utama.pdf', 'size' => '3.2 MB'],
                    ['name' => 'Berita Acara Stock Opname Q3.pdf', 'size' => '850 KB'],
                ],
            ],
            [
                'id' => 'WH-002',
                'name' => 'Gudang Situbondo',
                'location' => 'Jl. PB Sudirman No. 45',
                'area' => 'Situbondo',
                'capacity' => 30000,
                'available_stock' => 24870,
                'used_stock' => 5130,
                'status' => 'Aktif',
                'manager' => 'Ahmad Fauzi',
                'manager_role' => 'Hub Logistics Officer',
                'categories_count' => 3,
                'description' => 'Gudang hub regional area perkotaan Situbondo untuk percepatan perbaikan customer residensial.',
                'stock_items' => [
                    ['name' => 'ONT Huawei HG8245H', 'category' => 'Modem/ONT', 'qty' => 55, 'unit' => 'Unit'],
                    ['name' => 'Splitter 1:8 PLC', 'category' => 'Perangkat FO', 'qty' => 60, 'unit' => 'Pcs'],
                    ['name' => 'Router Mikrotik RB750Gr3', 'category' => 'Perangkat Jaringan', 'qty' => 5, 'unit' => 'Unit'],
                ],
                'history' => [
                    ['title' => 'Transfer Internal Masuk', 'date' => '06 Okt 2026 14:15', 'desc' => 'Menerima 20 unit ONT Huawei dari Gudang Utama.'],
                    ['title' => 'Pengeluaran Darurat', 'date' => '05 Okt 2026 11:00', 'desc' => 'Dikeluarkan 2 unit switch untuk recovery hotspot publik.'],
                ],
                'docs' => [
                    ['name' => 'Izin Operasional Gudang Hub.pdf', 'size' => '1.2 MB'],
                ],
            ],
            [
                'id' => 'WH-003',
                'name' => 'Gudang Besuki',
                'location' => 'Jl. Raya Besuki No. 78',
                'area' => 'Besuki',
                'capacity' => 20000,
                'available_stock' => 15420,
                'used_stock' => 4580,
                'status' => 'Maintenance',
                'manager' => 'Dimas Prayoga',
                'manager_role' => 'Logistics Staff',
                'categories_count' => 3,
                'description' => 'Gudang transit wilayah barat. Sedang proses penataan ulang rak penyimpanan dan perbaikan sistem sirkulasi udara.',
                'stock_items' => [
                    ['name' => 'Fiber Patchcord SC/UPC', 'category' => 'Accessories', 'qty' => 15, 'unit' => 'Pcs'],
                    ['name' => 'RJ45 Connector Cat6', 'category' => 'Accessories', 'qty' => 120, 'unit' => 'Pack'],
                    ['name' => 'Kabel FO 12 Core Armored', 'category' => 'Kabel', 'qty' => 2, 'unit' => 'Drum'],
                ],
                'history' => [
                    ['title' => 'Mulai Maintenance Fisik', 'date' => '07 Okt 2026 08:00', 'desc' => 'Renovasi rak bagian B dan penggantian palet kayu.'],
                    ['title' => 'Audit Keselamatan Kerja', 'date' => '02 Okt 2026 13:30', 'desc' => 'Pengecekan alat pemadam api ringan (APAR).'],
                ],
                'docs' => [
                    ['name' => 'Rencana Kerja Renovasi Rak.pdf', 'size' => '2.1 MB'],
                ],
            ],
            [
                'id' => 'WH-004',
                'name' => 'Gudang Lapangan',
                'location' => 'Lokasi Site A',
                'area' => 'Area Lapangan',
                'capacity' => 10000,
                'available_stock' => 5210,
                'used_stock' => 4790,
                'status' => 'Aktif',
                'manager' => 'Rizky Ananda',
                'manager_role' => 'Field Material Coordinator',
                'categories_count' => 2,
                'description' => 'Depo kontainer logistik cepat tanggap di lokasi project deployment jaringan FO baru.',
                'stock_items' => [
                    ['name' => 'Kabel Dropcore 1C 1000m', 'category' => 'Kabel', 'qty' => 60, 'unit' => 'Roll'],
                    ['name' => 'ODP Box 16 Core Outdoor', 'category' => 'Perangkat FO', 'qty' => 45, 'unit' => 'Unit'],
                    ['name' => 'Clamping Hook Tiang', 'category' => 'Accessories', 'qty' => 450, 'unit' => 'Pcs'],
                ],
                'history' => [
                    ['title' => 'Drop Material Proyek', 'date' => '07 Okt 2026 16:45', 'desc' => 'Drop 30 roll kabel dropcore untuk perluasan Site A.'],
                    ['title' => 'Inspeksi Harian', 'date' => '08 Okt 2026 07:30', 'desc' => 'Gembok kontainer dan segel pengaman dalam kondisi baik.'],
                ],
                'docs' => [
                    ['name' => 'Berita Acara Penempatan Depo.pdf', 'size' => '640 KB'],
                ],
            ],
        ];

        $summary = $summary ?? [
            'total_warehouses' => 4,
            'total_capacity'   => 120000,
            'available_stock'  => 98450,
            'used_stock'       => 21550,
        ];

        $locations = $locations ?? ['Situbondo', 'Besuki', 'Area Lapangan'];
        $statuses = $statuses ?? ['Aktif', 'Maintenance', 'Nonaktif'];

        $warehousesById = collect($warehouses)->keyBy('id')->all();
    @endphp

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700" role="status">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                <p class="font-semibold">Data belum tersimpan:</p>
                <ul class="mt-1 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        {{-- ============================================ --}}
        {{-- 1. HEADER WAREHOUSE                          --}}
        {{-- ============================================ --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-dark tracking-tight">Warehouse</h1>
                <p class="text-secondary text-sm mt-0.5">Kelola data gudang dan pergerakan stok</p>
            </div>
            <div>
                <button type="button" id="btn-open-create-modal"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-white text-sm font-medium hover:bg-blue-700 shadow-sm transition-colors">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Tambah Warehouse</span>
                </button>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- 2. SUMMARY CARDS                             --}}
        {{-- ============================================ --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Total Gudang --}}
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i data-lucide="warehouse" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Total Gudang</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">{{ $summary['total_warehouses'] }}</p>
                </div>
            </div>

            {{-- Total Kapasitas --}}
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i data-lucide="boxes" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Total Kapasitas</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">{{ number_format($summary['total_capacity']) }} <span class="text-xs font-normal text-secondary">unit</span></p>
                </div>
            </div>

            {{-- Stok Tersedia --}}
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Stok Tersedia</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">{{ number_format($summary['available_stock']) }} <span class="text-xs font-normal text-secondary">unit</span></p>
                </div>
            </div>

            {{-- Stok Terpakai --}}
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                    <i data-lucide="package-minus" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Stok Terpakai</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">{{ number_format($summary['used_stock']) }} <span class="text-xs font-normal text-secondary">unit</span></p>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- 3. LAYOUT UTAMA (TABEL + PANEL DETAIL)       --}}
        {{-- ============================================ --}}
        <div class="flex flex-col xl:flex-row items-start gap-5">

            {{-- ======================================== --}}
            {{-- KOLOM KIRI: TABEL WAREHOUSE              --}}
            {{-- ======================================== --}}
            <div id="table-container" class="w-full xl:flex-1 bg-white rounded-2xl border border-border shadow-[0_2px_6px_rgba(0,0,0,0.02)] overflow-hidden transition-all duration-300">

                {{-- Toolbar Filter & Search --}}
                <div class="p-4 border-b border-border space-y-3">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="relative flex-1 min-w-[220px]">
                            <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                            <input id="warehouse-search" type="text"
                                   placeholder="Cari nama gudang, kode, atau lokasi..."
                                   class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-border bg-surface focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        </div>

                        <button type="button" id="btn-toggle-filters"
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-border text-xs font-medium text-dark hover:bg-gray-50 transition-colors">
                            <i data-lucide="filter" class="w-3.5 h-3.5 text-secondary"></i>
                            <span>Filter</span>
                            <i data-lucide="chevron-down" class="w-3 h-3 text-gray-400"></i>
                        </button>

                        <select id="warehouse-sort"
                                class="text-xs rounded-xl border border-border bg-white px-3 py-2 text-dark focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <option value="code_asc">Kode Gudang (A-Z)</option>
                            <option value="capacity_desc">Kapasitas Tertinggi</option>
                            <option value="stock_desc">Stok Tersedia Terbanyak</option>
                        </select>
                    </div>

                    {{-- Dropdown Panel Filter Tambahan --}}
                    <div id="filter-dropdown-panel" class="hidden grid-cols-1 sm:grid-cols-3 gap-2.5 pt-2 border-t border-gray-100">
                        <select id="filter-location" class="text-xs rounded-lg border border-border bg-white px-2.5 py-1.5 text-dark">
                            <option value="">Semua Lokasi</option>
                            @foreach ($locations as $loc)
                                <option value="{{ $loc }}">{{ $loc }}</option>
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

                {{-- Tabel Data Gudang --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr class="text-secondary bg-[#FAFCFF] border-b border-border font-medium text-[11px] uppercase tracking-wider">
                                <th class="pl-4 py-3 w-8">
                                    <input id="check-all" type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary/20">
                                </th>
                                <th class="py-3 pr-3">Kode Gudang</th>
                                <th class="py-3 pr-3">Nama Gudang</th>
                                <th class="py-3 pr-3">Lokasi</th>
                                <th class="py-3 pr-3 text-right">Kapasitas</th>
                                <th class="py-3 pr-3 text-right">Stok Tersedia</th>
                                <th class="py-3 pr-3 text-center">Status</th>
                                <th class="py-3 pr-4 text-center w-8">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="warehouse-table-body" class="divide-y divide-gray-100">
                            @foreach ($warehouses as $idx => $wh)
                                @php
                                    $statusBadgeClass = match($wh['status']) {
                                        'Aktif'       => 'bg-emerald-50 text-emerald-600 border border-emerald-200/60',
                                        'Maintenance' => 'bg-amber-50 text-amber-600 border border-amber-200/60 font-semibold',
                                        default       => 'bg-slate-100 text-slate-500 border border-slate-200/60',
                                    };
                                @endphp
                                <tr class="warehouse-row cursor-pointer transition-colors hover:bg-blue-50/40 {{ $idx === 0 ? 'bg-blue-50/70 border-l-4 border-l-primary' : '' }}"
                                    data-id="{{ $wh['id'] }}"
                                    data-name="{{ $wh['name'] }}"
                                    data-area="{{ $wh['area'] }}"
                                    data-capacity="{{ $wh['capacity'] }}"
                                    data-stock="{{ $wh['available_stock'] }}"
                                    data-status="{{ $wh['status'] }}"
                                    data-search="{{ strtolower($wh['id'] . ' ' . $wh['name'] . ' ' . $wh['location'] . ' ' . $wh['area']) }}">
                                    <td class="pl-4 py-3">
                                        <input type="checkbox" class="row-checkbox rounded border-gray-300 text-primary focus:ring-primary/20">
                                    </td>
                                    <td class="py-3 pr-3 font-semibold text-primary whitespace-nowrap">
                                        {{ $wh['id'] }}
                                    </td>
                                    <td class="py-3 pr-3">
                                        <p class="font-medium text-dark">{{ $wh['name'] }}</p>
                                        <p class="text-[11px] text-secondary">{{ $wh['description'] }}</p>
                                    </td>
                                    <td class="py-3 pr-3 whitespace-nowrap text-secondary">
                                        {{ $wh['location'] }}
                                    </td>
                                    <td class="py-3 pr-3 text-right font-medium text-dark whitespace-nowrap">
                                        {{ number_format($wh['capacity']) }} unit
                                    </td>
                                    <td class="py-3 pr-3 text-right font-bold text-emerald-600 whitespace-nowrap">
                                        {{ number_format($wh['available_stock']) }} unit
                                    </td>
                                    <td class="py-3 pr-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium whitespace-nowrap {{ $statusBadgeClass }}">
                                            {{ $wh['status'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 pr-4 text-center">
                                        <button type="button" class="p-1 rounded-md text-gray-400 hover:text-dark hover:bg-gray-100" data-warehouse-action="edit" aria-label="Edit {{ $wh['name'] }}">
                                            <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                            <tr id="empty-state-row" class="hidden">
                                <td colspan="8" class="py-12 text-center text-gray-400">
                                    <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                                    Tidak ada gudang yang cocok dengan pencarian / filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="p-3.5 border-t border-border text-xs">
                    <p id="table-info" class="text-secondary">Menampilkan 1–{{ count($warehouses) }} dari {{ count($warehouses) }} gudang</p>
                </div>

            </div>

            {{-- ======================================== --}}
            {{-- KOLOM KANAN: PANEL DETAIL GUDANG         --}}
            {{-- ======================================== --}}
            <aside id="warehouse-detail-panel"
                   class="w-full xl:w-[480px] 2xl:w-[520px] bg-white rounded-2xl border border-border shadow-[0_2px_6px_rgba(0,0,0,0.02)] p-5 shrink-0 transition-all duration-300 xl:sticky xl:top-20">

                {{-- Header Detail Panel --}}
                <div class="flex items-start justify-between pb-4 border-b border-border">
                    <div class="flex items-start gap-3.5">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-primary flex items-center justify-center shrink-0 border border-blue-200/50">
                            <i data-lucide="warehouse" class="w-6 h-6"></i>
                        </div>
                        <div class="space-y-0.5">
                            <div class="flex items-center gap-2">
                                <span id="detail-wh-id" class="text-lg font-bold text-primary">WH-001</span>
                                <span id="detail-status-badge" class="px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-600 border border-emerald-200/60">Aktif</span>
                            </div>
                            <h2 id="detail-wh-name" class="text-base font-bold text-dark">Gudang Utama</h2>
                            <p class="text-xs text-secondary" id="detail-wh-desc">Gudang penyimpanan utama</p>
                        </div>
                    </div>

                    <button type="button" id="btn-close-detail"
                            class="p-1.5 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100 transition-colors"
                            title="Tutup Panel Detail">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                {{-- Ringkasan 3 Info Horizontal --}}
                <div class="grid grid-cols-3 gap-2 p-3 rounded-xl bg-surface border border-border/80 text-xs my-4">
                    <div>
                        <p class="text-[10px] text-secondary uppercase font-semibold">Lokasi</p>
                        <p id="detail-card-location" class="font-bold text-dark mt-0.5 truncate text-[11px]">Jl. Raya Situbondo</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-secondary uppercase font-semibold">Kapasitas</p>
                        <p id="detail-card-capacity" class="font-bold text-dark mt-0.5 text-[11px]">50,000 unit</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-secondary uppercase font-semibold">Stok Tersedia</p>
                        <p id="detail-card-stock" class="font-bold text-emerald-600 mt-0.5 text-[11px]">42,350 unit</p>
                    </div>
                </div>

                {{-- Progress Bar Utilisasi Kapasitas --}}
                <div class="p-3 rounded-xl border border-border bg-surface mb-4 space-y-1.5">
                    <div class="flex justify-between text-[11px]">
                        <span class="text-secondary font-medium">Penggunaan Kapasitas</span>
                        <span id="detail-utilization-text" class="font-bold text-dark">15.3% Terpakai</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                        <div id="detail-utilization-bar" class="bg-primary rounded-full h-2 transition-all duration-300" style="width: 15.3%"></div>
                    </div>
                    <div class="flex justify-between text-[10px] text-secondary pt-0.5">
                        <span>Terpakai: <b id="detail-used-label" class="text-danger font-semibold">7,650 unit</b></span>
                        <span>Sisa Ruang: <b id="detail-avail-label" class="text-emerald-600 font-semibold">42,350 unit</b></span>
                    </div>
                </div>

                {{-- 4 Tab Navigation --}}
                <div class="flex border-b border-border text-xs font-semibold mb-4 gap-6" id="panel-tabs">
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-primary text-primary transition-colors" data-target="tab-detail">Detail</button>
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-transparent text-secondary hover:text-dark transition-colors" data-target="tab-stock">Stok</button>
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-transparent text-secondary hover:text-dark transition-colors" data-target="tab-history">Riwayat</button>
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-transparent text-secondary hover:text-dark transition-colors" data-target="tab-docs">Dokumentasi</button>
                </div>

                {{-- Konten Tab Panel --}}
                <div class="space-y-4">

                    {{-- TAB 1: DETAIL --}}
                    <div id="tab-detail" class="tab-pane space-y-3 text-xs">
                        <div class="space-y-2">
                            <div>
                                <span class="text-secondary text-[11px] block">Kode Gudang:</span>
                                <span id="info-code" class="font-semibold text-dark">WH-001</span>
                            </div>
                            <div>
                                <span class="text-secondary text-[11px] block">Nama Gudang:</span>
                                <span id="info-name" class="font-semibold text-dark">Gudang Utama</span>
                            </div>
                            <div>
                                <span class="text-secondary text-[11px] block">Alamat / Lokasi:</span>
                                <span id="info-location" class="font-medium text-dark">Jl. Raya Situbondo No. 12</span>
                            </div>
                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div>
                                    <span class="text-secondary text-[11px] block">Kapasitas Maksimal:</span>
                                    <span id="info-capacity" class="font-bold text-dark">50,000 unit</span>
                                </div>
                                <div>
                                    <span class="text-secondary text-[11px] block">Stok Tersedia:</span>
                                    <span id="info-available" class="font-bold text-emerald-600">42,350 unit</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div>
                                    <span class="text-secondary text-[11px] block">Stok Terpakai:</span>
                                    <span id="info-used" class="font-bold text-danger">7,650 unit</span>
                                </div>
                                <div>
                                    <span class="text-secondary text-[11px] block">Status:</span>
                                    <span id="info-status" class="font-semibold text-emerald-600">Aktif</span>
                                </div>
                            </div>
                            <div>
                                <span class="text-secondary text-[11px] block">Deskripsi:</span>
                                <p id="info-description" class="text-dark bg-surface p-2.5 rounded-xl border border-border mt-1 leading-relaxed">
                                    Gudang utama untuk penyimpanan perangkat dan material operasional.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 2: STOK --}}
                    <div id="tab-stock" class="tab-pane hidden space-y-2.5 text-xs">
                        <p class="font-semibold text-dark text-xs mb-2">Item Produk Tersimpan di Gudang Ini:</p>
                        <div id="stock-items-list" class="space-y-2">
                            {{-- Diisi secara dinamis oleh JS --}}
                        </div>
                    </div>

                    {{-- TAB 3: RIWAYAT --}}
                    <div id="tab-history" class="tab-pane hidden space-y-3 text-xs">
                        <p class="font-semibold text-dark text-xs mb-2">Riwayat Aktivitas & Pergerakan Gudang:</p>
                        <div id="history-timeline" class="space-y-3 relative before:absolute before:inset-0 before:left-2 before:w-0.5 before:bg-gray-200">
                            {{-- Diisi secara dinamis oleh JS --}}
                        </div>
                    </div>

                    {{-- TAB 4: DOKUMENTASI --}}
                    <div id="tab-docs" class="tab-pane hidden space-y-2.5 text-xs">
                        <p class="font-semibold text-dark text-xs mb-2">Dokumentasi Denah & Berkas Gudang:</p>
                        <div id="docs-list" class="space-y-2">
                            {{-- Diisi secara dinamis oleh JS --}}
                        </div>
                    </div>

                    {{-- Status & Aksi --}}
                    <div class="p-3.5 rounded-xl border border-border bg-surface space-y-3">
                        <p class="font-semibold text-dark text-xs">Status & Aksi</p>
                        <select id="action-status-select" class="w-full text-xs rounded-lg border border-border bg-white px-3 py-2 text-dark focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <option value="Aktif">Aktif</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" id="btn-trigger-edit" class="py-2 px-3 rounded-lg bg-primary text-white text-xs font-semibold hover:bg-blue-700 shadow-sm transition-colors text-center">
                                Edit Gudang
                            </button>
                            <button type="button" id="btn-trigger-update-stock" class="py-2 px-3 rounded-lg bg-white border border-border text-dark text-xs font-medium hover:bg-gray-50 transition-colors text-center">
                                Update Stok
                            </button>
                            <button type="button" id="btn-trigger-view-history" class="py-2 px-3 rounded-lg bg-white border border-border text-dark text-xs font-medium hover:bg-gray-50 transition-colors text-center">
                                Lihat Riwayat
                            </button>
                            <button type="button" id="btn-trigger-delete" class="py-2 px-3 rounded-lg bg-white border border-red-200 text-danger text-xs font-medium hover:bg-red-50 transition-colors text-center">
                                Hapus Gudang
                            </button>
                        </div>
                    </div>

                    {{-- Quick Info --}}
                    <div class="p-3.5 rounded-xl border border-border bg-surface space-y-2.5 text-xs">
                        <p class="font-semibold text-dark text-xs">Quick Info</p>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-secondary text-[11px]">Lokasi:</p>
                                <p id="quick-location" class="font-medium text-dark">Jl. Raya Situbondo No. 12</p>
                            </div>
                            <span class="text-secondary text-[11px]">—</span>
                        </div>
                        <div class="flex items-center justify-between border-t border-gray-200/60 pt-2">
                            <div>
                                <p class="text-secondary text-[11px]">Kapasitas:</p>
                                <p id="quick-capacity" class="font-medium text-dark">50,000 unit</p>
                            </div>
                            <span class="text-secondary text-[11px]">—</span>
                        </div>
                        <div class="flex items-center justify-between border-t border-gray-200/60 pt-2">
                            <div>
                                <p class="text-secondary text-[11px]">Pengelola:</p>
                                <p id="quick-manager" class="font-medium text-dark">Budi Santoso</p>
                                <p id="quick-manager-role" class="text-[10px] text-secondary">Warehouse Staff</p>
                            </div>
                            <span class="w-6 h-6 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-[10px]">BS</span>
                        </div>
                        <div class="border-t border-gray-200/60 pt-2 text-[11px] text-secondary">
                            <p id="quick-category-note">Gudang ini menyimpan 4 kategori produk utama.</p>
                        </div>
                    </div>

                </div>

            </aside>

        </div>

    </div>

    {{-- ============================================ --}}
    {{-- 4. MODAL TAMBAH WAREHOUSE                    --}}
    {{-- ============================================ --}}
    <div id="modal-create-warehouse" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs hidden">
        <div class="bg-white rounded-2xl border border-border shadow-2xl max-w-lg w-full overflow-hidden animate-in fade-in duration-150">
            <div class="p-4 border-b border-border flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-dark">Tambah Warehouse Baru</h3>
                    <p class="text-secondary text-xs">Daftarkan data fasilitas penyimpanan stok</p>
                </div>
                <button type="button" id="btn-close-create-modal" class="p-1 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form id="form-create-warehouse" action="{{ route('warehouse.store') }}" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1">Kode Gudang</label>
                        <input type="text" id="create-code" value="{{ $warehousePreview }}" readonly
                               class="w-full rounded-xl border border-border bg-gray-50 text-gray-500 px-3 py-2 text-xs cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Nama Gudang <span class="text-danger">*</span></label>
                        <input type="text" id="create-name" name="name" placeholder="Contoh: Gudang Transit Timur" required
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-dark mb-1">Lokasi / Alamat Lengkap <span class="text-danger">*</span></label>
                    <input type="text" id="create-location" name="address" placeholder="Jl. Pelabuhan Jangkar No. 9" required
                           class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1">Area Wilayah</label>
                        <select id="create-area" name="area" required class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                            @foreach ($locations as $loc)
                                <option value="{{ $loc }}">{{ $loc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Kapasitas (Unit)</label>
                        <input type="number" id="create-capacity" name="capacity" min="1" value="15000" required
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Status Awal</label>
                        <select id="create-status" name="status" required class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                            <option value="Aktif">Aktif</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-dark mb-1">Pengelola / PIC</label>
                    <input type="text" id="create-manager" name="manager_name" placeholder="Nama staf pengelola gudang"
                           class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>

                <div>
                    <label class="block font-medium text-dark mb-1">Deskripsi Fasilitas</label>
                    <textarea id="create-desc" name="description" rows="2" placeholder="Keterangan peruntukan atau spesifikasi gudang..."
                              class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
                </div>

                <div class="pt-3 border-t border-border flex items-center justify-end gap-2">
                    <button type="button" id="btn-cancel-create-modal" class="px-3.5 py-2 rounded-xl border border-border text-secondary hover:bg-gray-50 font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-blue-700 shadow-sm">Simpan Gudang</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- 5. MODAL EDIT GUDANG                         --}}
    {{-- ============================================ --}}
    <div id="modal-edit-warehouse" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs hidden">
        <div class="bg-white rounded-2xl border border-border shadow-2xl max-w-lg w-full overflow-hidden animate-in fade-in duration-150">
            <div class="p-4 border-b border-border flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-dark">Edit Data Gudang</h3>
                    <p class="text-secondary text-xs">Ubah rincian informasi dan kapasitas gudang</p>
                </div>
                <button type="button" id="btn-close-edit-modal" class="p-1 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form id="form-edit-warehouse" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit-id-hidden">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1">Kode Gudang</label>
                        <input type="text" id="edit-code" readonly
                               class="w-full rounded-xl border border-border bg-gray-50 text-gray-500 px-3 py-2 text-xs cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Status</label>
                        <select id="edit-status" name="status" required class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                            <option value="Aktif">Aktif</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-dark mb-1">Nama Gudang</label>
                    <input type="text" id="edit-name" name="name" required
                           class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>

                <div>
                    <label class="block font-medium text-dark mb-1">Lokasi Alamat</label>
                    <input type="text" id="edit-location" name="address" required
                           class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1">Kapasitas (Unit)</label>
                        <input type="number" id="edit-capacity" name="capacity" min="1" required
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Pengelola</label>
                        <input type="text" id="edit-manager" name="manager_name"
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-dark mb-1">Area Wilayah</label>
                    <select id="edit-area" name="area" required class="w-full rounded-xl border border-border px-3 py-2 text-xs">
                        @foreach ($locations as $loc)
                            <option value="{{ $loc }}">{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1">Deskripsi</label>
                    <textarea id="edit-desc" name="description" rows="2"
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
    {{-- 6. MODAL UPDATE STOK                         --}}
    {{-- ============================================ --}}
    <div id="modal-update-stock" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs hidden">
        <div class="bg-white rounded-2xl border border-border shadow-2xl max-w-md w-full overflow-hidden animate-in fade-in duration-150">
            <div class="p-4 border-b border-border flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-dark">Update Stok Gudang</h3>
                    <p class="text-secondary text-xs">Catat penyesuaian stok atau pergerakan material</p>
                </div>
                <button type="button" id="btn-close-stock-modal" class="p-1 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form id="form-update-stock" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-medium text-dark mb-1">Gudang Tujuan</label>
                    <input type="text" id="stock-modal-wh-name" readonly
                           class="w-full rounded-xl border border-border bg-gray-50 text-gray-600 px-3 py-2 text-xs">
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1">Jenis Aktivitas Stok</label>
                    <select id="stock-movement-type" name="type" required class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                        <option value="Masuk">Stok Masuk</option>
                        <option value="Keluar">Stok Keluar</option>
                        <option value="Transfer">Transfer Antar Gudang</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1">Produk</label>
                    <select name="inventory_item_id" required class="w-full rounded-xl border border-border px-3 py-2 text-xs">
                        @foreach ($inventoryItems as $item)
                            <option value="{{ $item->id }}">{{ $item->sku }} - {{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1">Jumlah</label>
                    <input type="number" name="quantity" min="1" required class="w-full rounded-xl border border-border px-3 py-2 text-xs">
                </div>
                <div id="transfer-destination-field" class="hidden">
                    <label class="block font-medium text-dark mb-1">Gudang Tujuan</label>
                    <select name="to_warehouse_id" id="stock-destination" class="w-full rounded-xl border border-border px-3 py-2 text-xs">
                        <option value="">Pilih gudang tujuan</option>
                        @foreach ($warehouseOptions as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1">Nomor Referensi Dokumen</label>
                    <input type="text" name="reference" placeholder="Contoh: PO-2026-092 / SJ-088"
                           class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1">Catatan Mutasi</label>
                    <textarea name="note" rows="2" placeholder="Rincian material yang diupdate..."
                              class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20"></textarea>
                </div>
                <div class="pt-3 border-t border-border flex items-center justify-end gap-2">
                    <button type="button" id="btn-cancel-stock-modal" class="px-3.5 py-2 rounded-xl border border-border text-secondary hover:bg-gray-50 font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-blue-700 shadow-sm">Simpan Update</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Toast Notifikasi --}}
    <div id="toast-notification"
         class="fixed bottom-5 right-5 z-50 hidden max-w-sm rounded-xl bg-dark text-white text-xs px-4 py-3 shadow-xl transition-all">
    </div>
    <form id="form-delete-warehouse" method="POST" class="hidden">@csrf @method('DELETE')</form>
    <form id="form-warehouse-status" method="POST" class="hidden">@csrf @method('PATCH')<input type="hidden" name="status" id="warehouse-status-value"></form>

    {{-- ============================================ --}}
    {{-- 7. JAVASCRIPT LOGIC INTERAKTIF              --}}
    {{-- ============================================ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Data gudang dari Blade
            let warehouseList = @json($warehouses);
            let warehouseMap = {};
            warehouseList.forEach(w => { warehouseMap[w.id] = w; });

            let currentSelectedId = warehouseList[0]?.id || null;

            // Toast helper
            let toastTimer;
            function showToast(message) {
                const toast = document.getElementById('toast-notification');
                toast.textContent = message;
                toast.classList.remove('hidden');
                clearTimeout(toastTimer);
                toastTimer = setTimeout(() => toast.classList.add('hidden'), 2600);
            }

            // 1. Search & Filter & Sort
            const searchInput = document.getElementById('warehouse-search');
            const locationFilter = document.getElementById('filter-location');
            const statusFilter = document.getElementById('filter-status');
            const sortSelect = document.getElementById('warehouse-sort');
            const rows = Array.from(document.querySelectorAll('.warehouse-row'));
            const tbody = document.getElementById('warehouse-table-body');
            const emptyState = document.getElementById('empty-state-row');
            const tableInfo = document.getElementById('table-info');

            function applyFilters() {
                const q = searchInput.value.toLowerCase().trim();
                const loc = locationFilter.value;
                const st = statusFilter.value;
                const sortMode = sortSelect.value;

                // Sorting
                const sortedRows = rows.slice().sort((a, b) => {
                    if (sortMode === 'code_asc') return a.dataset.id.localeCompare(b.dataset.id);
                    if (sortMode === 'capacity_desc') return parseInt(b.dataset.capacity) - parseInt(a.dataset.capacity);
                    if (sortMode === 'stock_desc') return parseInt(b.dataset.stock) - parseInt(a.dataset.stock);
                    return 0;
                });

                sortedRows.forEach(r => tbody.insertBefore(r, emptyState));

                let visibleCount = 0;
                sortedRows.forEach(r => {
                    const matchQ = !q || r.dataset.search.includes(q);
                    const matchLoc = !loc || r.dataset.area === loc;
                    const matchSt = !st || r.dataset.status === st;

                    const match = matchQ && matchLoc && matchSt;
                    r.classList.toggle('hidden', !match);
                    if (match) visibleCount++;
                });

                emptyState.classList.toggle('hidden', visibleCount > 0);
                tableInfo.textContent = `Menampilkan ${visibleCount} dari ${rows.length} gudang`;
            }

            [searchInput, locationFilter, statusFilter, sortSelect].forEach(el => {
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
                locationFilter.value = '';
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
            const detailPanel = document.getElementById('warehouse-detail-panel');

            function populateDetail(whId) {
                const w = warehouseMap[whId];
                if (!w) return;
                currentSelectedId = whId;

                // Header
                document.getElementById('detail-wh-id').textContent = w.id;
                document.getElementById('detail-wh-name').textContent = w.name;
                document.getElementById('detail-wh-desc').textContent = w.description;

                const statusBadge = document.getElementById('detail-status-badge');
                statusBadge.textContent = w.status;
                if (w.status === 'Aktif') {
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-600 border border-emerald-200/60';
                } else if (w.status === 'Maintenance') {
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-600 border border-amber-200/60 font-semibold';
                } else {
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-500 border border-slate-200/60';
                }

                // 3 Card Ringkas
                document.getElementById('detail-card-location').textContent = w.location;
                document.getElementById('detail-card-capacity').textContent = Number(w.capacity).toLocaleString('id-ID') + ' unit';
                document.getElementById('detail-card-stock').textContent = Number(w.available_stock).toLocaleString('id-ID') + ' unit';

                // Progress Bar
                const pctUsed = Math.min(100, Math.round((w.used_stock / w.capacity) * 1000) / 10);
                document.getElementById('detail-utilization-text').textContent = pctUsed + '% Terpakai';
                document.getElementById('detail-utilization-bar').style.width = pctUsed + '%';
                document.getElementById('detail-used-label').textContent = Number(w.used_stock).toLocaleString('id-ID') + ' unit';
                document.getElementById('detail-avail-label').textContent = Number(w.available_stock).toLocaleString('id-ID') + ' unit';

                // Tab Detail
                document.getElementById('info-code').textContent = w.id;
                document.getElementById('info-name').textContent = w.name;
                document.getElementById('info-location').textContent = w.location;
                document.getElementById('info-capacity').textContent = Number(w.capacity).toLocaleString('id-ID') + ' unit';
                document.getElementById('info-available').textContent = Number(w.available_stock).toLocaleString('id-ID') + ' unit';
                document.getElementById('info-used').textContent = Number(w.used_stock).toLocaleString('id-ID') + ' unit';
                document.getElementById('info-status').textContent = w.status;
                document.getElementById('info-description').textContent = w.description;

                // Tab Stok
                const stockList = document.getElementById('stock-items-list');
                const items = w.stock_items || [];
                stockList.innerHTML = items.length ? items.map(it => `
                    <div class="p-2.5 rounded-xl border border-border bg-surface flex items-center justify-between">
                        <div>
                            <p class="font-medium text-dark">${it.name}</p>
                            <span class="text-[10px] text-primary font-medium">${it.category}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-dark text-sm">${it.qty}</span>
                            <span class="text-[10px] text-secondary block">${it.unit}</span>
                        </div>
                    </div>
                `).join('') : '<p class="text-secondary text-center py-4">Belum ada item tercatat.</p>';

                // Tab Riwayat
                const timeline = document.getElementById('history-timeline');
                const historyData = w.history || [];
                timeline.innerHTML = historyData.length ? historyData.map((h, i) => `
                    <div class="relative flex items-start gap-3 pl-6">
                        <span class="absolute left-1 top-1 w-2.5 h-2.5 rounded-full ${i === 0 ? 'bg-primary ring-4 ring-white' : 'bg-gray-300 ring-4 ring-white'}"></span>
                        <div>
                            <p class="font-semibold text-dark">${h.title}</p>
                            <p class="text-[11px] text-secondary mt-0.5">${h.desc}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">${h.date}</p>
                        </div>
                    </div>
                `).join('') : '<p class="text-secondary text-center py-4">Belum ada catatan aktivitas.</p>';

                // Tab Dokumen
                const docsList = document.getElementById('docs-list');
                const docsData = w.docs || [];
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
                document.getElementById('quick-location').textContent = w.location;
                document.getElementById('quick-capacity').textContent = Number(w.capacity).toLocaleString('id-ID') + ' unit';
                document.getElementById('quick-manager').textContent = w.manager;
                document.getElementById('quick-manager-role').textContent = w.manager_role;
                document.getElementById('quick-category-note').textContent = `Gudang ini menyimpan ${w.categories_count || 4} kategori produk utama.`;

                document.getElementById('action-status-select').value = w.status;

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

            document.querySelectorAll('[data-warehouse-action="edit"]').forEach(button => {
                button.addEventListener('click', () => {
                    button.closest('.warehouse-row')?.click();
                    document.getElementById('btn-trigger-edit').click();
                });
            });

            // Tutup Panel Detail
            document.getElementById('btn-close-detail').addEventListener('click', () => {
                detailPanel.classList.add('hidden');
                rows.forEach(r => r.classList.remove('bg-blue-50/70', 'border-l-4', 'border-l-primary'));
            });

            // 3. Tab Switching
            function switchTab(targetId) {
                document.querySelectorAll('#panel-tabs .tab-btn').forEach(b => {
                    b.classList.remove('border-primary', 'text-primary');
                    b.classList.add('border-transparent', 'text-secondary');
                });
                const activeBtn = document.querySelector(`#panel-tabs .tab-btn[data-target="${targetId}"]`);
                if (activeBtn) {
                    activeBtn.classList.add('border-primary', 'text-primary');
                    activeBtn.classList.remove('border-transparent', 'text-secondary');
                }

                document.querySelectorAll('.tab-pane').forEach(p => p.classList.add('hidden'));
                const activePane = document.getElementById(targetId);
                if (activePane) activePane.classList.remove('hidden');
            }

            document.querySelectorAll('#panel-tabs .tab-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    switchTab(btn.dataset.target);
                });
            });

            // Tombol "Lihat Riwayat" pada card aksi otomatis berpindah ke tab Riwayat
            document.getElementById('btn-trigger-view-history').addEventListener('click', () => {
                switchTab('tab-history');
                showToast('Membuka tab Riwayat gudang.');
            });

            // 4. Modal Tambah Warehouse
            const createModal = document.getElementById('modal-create-warehouse');
            document.getElementById('btn-open-create-modal').addEventListener('click', () => {
                createModal.classList.remove('hidden');
            });
            const closeCreateModal = () => createModal.classList.add('hidden');
            document.getElementById('btn-close-create-modal').addEventListener('click', closeCreateModal);
            document.getElementById('btn-cancel-create-modal').addEventListener('click', closeCreateModal);

            // 5. Modal Edit Gudang
            const editModal = document.getElementById('modal-edit-warehouse');
            const editForm = document.getElementById('form-edit-warehouse');
            document.getElementById('btn-trigger-edit').addEventListener('click', () => {
                const current = warehouseMap[currentSelectedId];
                if (!current) return;

                document.getElementById('edit-id-hidden').value = current.id;
                document.getElementById('edit-code').value = current.id;
                editForm.action = `{{ url('/warehouse') }}/${encodeURIComponent(current.id)}`;
                document.getElementById('edit-name').value = current.name;
                document.getElementById('edit-location').value = current.location;
                document.getElementById('edit-capacity').value = current.capacity;
                document.getElementById('edit-manager').value = current.manager;
                document.getElementById('edit-status').value = current.status;
                document.getElementById('edit-area').value = current.area;
                document.getElementById('edit-desc').value = current.description;

                editModal.classList.remove('hidden');
            });

            const closeEditModal = () => editModal.classList.add('hidden');
            document.getElementById('btn-close-edit-modal').addEventListener('click', closeEditModal);
            document.getElementById('btn-cancel-edit-modal').addEventListener('click', closeEditModal);

            // 6. Modal Update Stok
            const stockModal = document.getElementById('modal-update-stock');
            const stockForm = document.getElementById('form-update-stock');
            document.getElementById('btn-trigger-update-stock').addEventListener('click', () => {
                const current = warehouseMap[currentSelectedId];
                if (!current) return;
                document.getElementById('stock-modal-wh-name').value = `${current.name} (${current.id})`;
                stockForm.action = `{{ url('/warehouse') }}/${encodeURIComponent(current.id)}/movements`;
                stockModal.classList.remove('hidden');
            });
            const closeStockModal = () => stockModal.classList.add('hidden');
            document.getElementById('btn-close-stock-modal').addEventListener('click', closeStockModal);
            document.getElementById('btn-cancel-stock-modal').addEventListener('click', closeStockModal);

            const movementType = document.getElementById('stock-movement-type');
            const destinationField = document.getElementById('transfer-destination-field');
            const destinationSelect = document.getElementById('stock-destination');
            movementType.addEventListener('change', () => {
                const isTransfer = movementType.value === 'Transfer';
                destinationField.classList.toggle('hidden', !isTransfer);
                destinationSelect.required = isTransfer;
            });
            movementType.dispatchEvent(new Event('change'));

            // 7. Hapus Gudang
            document.getElementById('btn-trigger-delete').addEventListener('click', () => {
                const current = warehouseMap[currentSelectedId];
                if (!current) return;

                if (confirm(`Apakah Anda yakin ingin menghapus gudang "${current.name}" (${current.id})? Tindakan ini bersifat permanen.`)) {
                    const deleteForm = document.getElementById('form-delete-warehouse');
                    deleteForm.action = `{{ url('/warehouse') }}/${encodeURIComponent(current.id)}`;
                    deleteForm.submit();
                }
            });

            document.getElementById('action-status-select').addEventListener('change', (event) => {
                if (!currentSelectedId) return;
                const statusForm = document.getElementById('form-warehouse-status');
                statusForm.action = `{{ url('/warehouse') }}/${encodeURIComponent(currentSelectedId)}/status`;
                document.getElementById('warehouse-status-value').value = event.target.value;
                statusForm.submit();
            });

            if (currentSelectedId) populateDetail(currentSelectedId);
            @if ($errors->any())
                showToast('Periksa pesan validasi untuk memperbaiki data.');
            @endif

            // Refresh icons
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</x-app-layout>
