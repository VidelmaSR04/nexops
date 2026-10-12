<x-app-layout>

    {{-- ============================================ --}}
    {{-- DATA DUMMY INVENTORY (Frontend Prototype)     --}}
    {{-- ============================================ --}}
    @php
        $products = [
            [
                'id' => 'PRD-001',
                'name' => 'ONT ZTE F670L',
                'category' => 'Modem/ONT',
                'unit' => 'Unit',
                'total_stock' => 120,
                'min_stock' => 10,
                'status' => 'Aktif',
                'location' => 'Gudang Utama',
                'address' => 'Jl. Raya Situbondo No. 12',
                'serial_example' => 'ZTE001234',
                'description' => 'Optical Network Terminal (ONT) dual-band GPON dengan 4 port Gigabit Ethernet dan WiFi 2.4/5GHz.',
                'warehouses' => [
                    ['name' => 'Gudang Utama', 'stock' => 70, 'min' => 10],
                    ['name' => 'Gudang Situbondo', 'stock' => 30, 'min' => 10],
                    ['name' => 'Gudang Besuki', 'stock' => 20, 'min' => 5],
                ],
                'movements' => [
                    ['date' => '08 Okt 2026 09:43', 'type' => 'Keluar', 'qty' => 1, 'ref' => 'Ticket #TK-1025', 'user' => 'Budi'],
                    ['date' => '06 Okt 2026 14:10', 'type' => 'Masuk', 'qty' => 50, 'ref' => 'PO-2026-089', 'user' => 'Eka Pratama'],
                    ['date' => '04 Okt 2026 11:20', 'type' => 'Keluar', 'qty' => 2, 'ref' => 'Ticket #TK-1018', 'user' => 'Dimas'],
                ],
                'related' => [
                    'active_assets' => 14,
                    'open_tickets' => 2,
                ]
            ],
            [
                'id' => 'PRD-002',
                'name' => 'ONT Huawei HG8245H',
                'category' => 'Modem/ONT',
                'unit' => 'Unit',
                'total_stock' => 85,
                'min_stock' => 10,
                'status' => 'Aktif',
                'location' => 'Gudang Situbondo',
                'address' => 'Jl. Basuki Rahmat No. 45, Situbondo',
                'serial_example' => 'HWT778120',
                'description' => 'EchoLife HG8245H GPON ONT dengan 4 port GE, 2 port POTS, dan 1 port USB.',
                'warehouses' => [
                    ['name' => 'Gudang Situbondo', 'stock' => 55, 'min' => 10],
                    ['name' => 'Gudang Utama', 'stock' => 30, 'min' => 10],
                ],
                'movements' => [
                    ['date' => '07 Okt 2026 15:30', 'type' => 'Keluar', 'qty' => 1, 'ref' => 'Ticket #TK-1022', 'user' => 'Rizky'],
                ],
                'related' => [
                    'active_assets' => 9,
                    'open_tickets' => 1,
                ]
            ],
            [
                'id' => 'PRD-003',
                'name' => 'RJ45 Connector',
                'category' => 'Accessories',
                'unit' => 'Pack',
                'total_stock' => 420,
                'min_stock' => 100,
                'status' => 'Aktif',
                'location' => 'Gudang Utama',
                'address' => 'Jl. Raya Situbondo No. 12',
                'serial_example' => 'CAT6-CON-420',
                'description' => 'Konektor RJ45 Cat6 modular plug berlapis emas 50 micron untuk kabel UTP/STP.',
                'warehouses' => [
                    ['name' => 'Gudang Utama', 'stock' => 300, 'min' => 70],
                    ['name' => 'Gudang Besuki', 'stock' => 120, 'min' => 30],
                ],
                'movements' => [
                    ['date' => '05 Okt 2026 10:00', 'type' => 'Keluar', 'qty' => 20, 'ref' => 'Maintenance Jaringan', 'user' => 'Ahmad'],
                ],
                'related' => [
                    'active_assets' => 0,
                    'open_tickets' => 0,
                ]
            ],
            [
                'id' => 'PRD-004',
                'name' => 'Fiber Patchcord',
                'category' => 'Accessories',
                'unit' => 'Pcs',
                'total_stock' => 65,
                'min_stock' => 20,
                'status' => 'Low Stock',
                'location' => 'Gudang Besuki',
                'address' => 'Jl. Raya Besuki Timur No. 8',
                'serial_example' => 'PC-SCUPC-3M',
                'description' => 'Patchcord Single Mode SC/UPC to SC/UPC simplex panjang 3 meter diameter 3.0mm.',
                'warehouses' => [
                    ['name' => 'Gudang Besuki', 'stock' => 15, 'min' => 10],
                    ['name' => 'Gudang Utama', 'stock' => 50, 'min' => 10],
                ],
                'movements' => [
                    ['date' => '08 Okt 2026 08:55', 'type' => 'Keluar', 'qty' => 1, 'ref' => 'Ticket #TK-1025', 'user' => 'Budi'],
                ],
                'related' => [
                    'active_assets' => 65,
                    'open_tickets' => 2,
                ]
            ],
            [
                'id' => 'PRD-005',
                'name' => 'Adapter 12V',
                'category' => 'Accessories',
                'unit' => 'Pcs',
                'total_stock' => 48,
                'min_stock' => 25,
                'status' => 'Low Stock',
                'location' => 'Gudang Utama',
                'address' => 'Jl. Raya Situbondo No. 12',
                'serial_example' => 'ADP-12V1.5A',
                'description' => 'Power Adaptor Switching AC 100-240V ke DC 12V 1.5A jack standard 5.5mm.',
                'warehouses' => [
                    ['name' => 'Gudang Utama', 'stock' => 48, 'min' => 25],
                ],
                'movements' => [
                    ['date' => '07 Okt 2026 20:42', 'type' => 'Keluar', 'qty' => 1, 'ref' => 'Ticket #TK-1019', 'user' => 'Ahmad'],
                ],
                'related' => [
                    'active_assets' => 40,
                    'open_tickets' => 1,
                ]
            ],
            [
                'id' => 'PRD-006',
                'name' => 'Splitter 1:8',
                'category' => 'Perangkat FO',
                'unit' => 'Pcs',
                'total_stock' => 200,
                'min_stock' => 50,
                'status' => 'Aktif',
                'location' => 'Gudang Utama',
                'address' => 'Jl. Raya Situbondo No. 12',
                'serial_example' => 'PLC-1x8-SCUPC',
                'description' => 'PLC Optical Splitter 1:8 SC/UPC box type untuk distribusi Fiber to the Home (FTTH).',
                'warehouses' => [
                    ['name' => 'Gudang Utama', 'stock' => 140, 'min' => 30],
                    ['name' => 'Gudang Situbondo', 'stock' => 60, 'min' => 20],
                ],
                'movements' => [
                    ['date' => '03 Okt 2026 13:00', 'type' => 'Masuk', 'qty' => 100, 'ref' => 'PO-2026-077', 'user' => 'Eka Pratama'],
                ],
                'related' => [
                    'active_assets' => 18,
                    'open_tickets' => 0,
                ]
            ],
            [
                'id' => 'PRD-007',
                'name' => 'ODP Box',
                'category' => 'Perangkat FO',
                'unit' => 'Unit',
                'total_stock' => 75,
                'min_stock' => 20,
                'status' => 'Aktif',
                'location' => 'Gudang Asembagus',
                'address' => 'Jl. Pelabuhan Jangkar No. 3',
                'serial_example' => 'ODP-8C-OUTDOOR',
                'description' => 'Optical Distribution Point 8/16 Core IP65 Waterproof dengan kunci proteksi luar ruangan.',
                'warehouses' => [
                    ['name' => 'Gudang Asembagus', 'stock' => 45, 'min' => 10],
                    ['name' => 'Gudang Utama', 'stock' => 30, 'min' => 10],
                ],
                'movements' => [
                    ['date' => '02 Okt 2026 09:15', 'type' => 'Keluar', 'qty' => 2, 'ref' => 'Ekspansi Jaringan Cluster A', 'user' => 'Dimas'],
                ],
                'related' => [
                    'active_assets' => 12,
                    'open_tickets' => 0,
                ]
            ],
            [
                'id' => 'PRD-008',
                'name' => 'Kabel Dropcore',
                'category' => 'Kabel',
                'unit' => 'Roll',
                'total_stock' => 150,
                'min_stock' => 30,
                'status' => 'Aktif',
                'location' => 'Gudang Utama',
                'address' => 'Jl. Raya Situbondo No. 12',
                'serial_example' => 'DC-1C-1000M',
                'description' => 'Kabel Drop Fiber Optik 1 Core 3 Seling Kawat Baja panjang 1000m/roll.',
                'warehouses' => [
                    ['name' => 'Gudang Utama', 'stock' => 90, 'min' => 20],
                    ['name' => 'Gudang Banyuglugur', 'stock' => 60, 'min' => 10],
                ],
                'movements' => [
                    ['date' => '07 Okt 2026 14:10', 'type' => 'Keluar', 'qty' => 1, 'ref' => 'Ticket #TK-1016', 'user' => 'Fajar'],
                ],
                'related' => [
                    'active_assets' => 85,
                    'open_tickets' => 1,
                ]
            ],
            [
                'id' => 'PRD-009',
                'name' => 'Patch Panel',
                'category' => 'Perangkat Jaringan',
                'unit' => 'Unit',
                'total_stock' => 35,
                'min_stock' => 10,
                'status' => 'Aktif',
                'location' => 'Gudang Utama',
                'address' => 'Jl. Raya Situbondo No. 12',
                'serial_example' => 'PP-24P-CAT6',
                'description' => 'Patch Panel 24 Port 1U 19-inch Rackmount Cat6 UTP with rear cable manager.',
                'warehouses' => [
                    ['name' => 'Gudang Utama', 'stock' => 35, 'min' => 10],
                ],
                'movements' => [
                    ['date' => '01 Okt 2026 16:45', 'type' => 'Masuk', 'qty' => 15, 'ref' => 'PO-2026-065', 'user' => 'Eka Pratama'],
                ],
                'related' => [
                    'active_assets' => 8,
                    'open_tickets' => 0,
                ]
            ],
            [
                'id' => 'PRD-010',
                'name' => 'Router Mikrotik',
                'category' => 'Perangkat Jaringan',
                'unit' => 'Unit',
                'total_stock' => 20,
                'min_stock' => 5,
                'status' => 'Low Stock',
                'location' => 'Gudang Utama',
                'address' => 'Jl. Raya Situbondo No. 12',
                'serial_example' => 'RB750Gr3-HEX',
                'description' => 'RouterBOARD hEX 5x Gigabit Ethernet, Dual Core 880MHz CPU, 256MB RAM, RouterOS L4.',
                'warehouses' => [
                    ['name' => 'Gudang Utama', 'stock' => 15, 'min' => 5],
                    ['name' => 'Gudang Situbondo', 'stock' => 5, 'min' => 2],
                ],
                'movements' => [
                    ['date' => '08 Okt 2026 07:45', 'type' => 'Keluar', 'qty' => 1, 'ref' => 'Ticket #TK-1023', 'user' => 'Dimas'],
                ],
                'related' => [
                    'active_assets' => 22,
                    'open_tickets' => 1,
                ]
            ],
        ];

        $summary = [
            'total_products' => 248,
            'total_stock'    => 12480,
            'low_stock'      => 13,
            'warehouse'      => 4,
        ];

        $categories = [
            'Modem/ONT',
            'Accessories',
            'Perangkat FO',
            'Kabel',
            'Perangkat Jaringan',
        ];

        $statuses = [
            'Aktif',
            'Low Stock',
            'Habis',
            'Nonaktif',
        ];

        $productsById = collect($products)->keyBy('id')->all();
    @endphp

    <div class="space-y-6">

        {{-- ============================================ --}}
        {{-- 1. HEADER INVENTORY                          --}}
        {{-- ============================================ --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-dark tracking-tight">Inventory</h1>
                <p class="text-secondary text-sm mt-0.5">Kelola data produk dan stok persediaan</p>
            </div>
            <div>
                <button type="button" id="btn-open-create-modal"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-white text-sm font-medium hover:bg-blue-700 shadow-sm transition-colors">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Tambah Produk</span>
                </button>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- 2. SUMMARY CARDS                             --}}
        {{-- ============================================ --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Total Produk --}}
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i data-lucide="package" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Total Produk</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">{{ number_format($summary['total_products']) }}</p>
                </div>
            </div>

            {{-- Total Stok --}}
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center shrink-0">
                    <i data-lucide="boxes" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Total Stok</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">{{ number_format($summary['total_stock']) }}</p>
                </div>
            </div>

            {{-- Low Stock --}}
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Low Stock</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">{{ $summary['low_stock'] }}</p>
                </div>
            </div>

            {{-- Warehouse --}}
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i data-lucide="warehouse" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Warehouse</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">{{ $summary['warehouse'] }}</p>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- 3. LAYOUT UTAMA (TABEL + DETAIL PRODUK)      --}}
        {{-- ============================================ --}}
        <div class="flex flex-col xl:flex-row items-start gap-5">

            {{-- ======================================== --}}
            {{-- KOLOM KIRI: TABEL INVENTORY              --}}
            {{-- ======================================== --}}
            <div id="table-container" class="w-full xl:flex-1 bg-white rounded-2xl border border-border shadow-[0_2px_6px_rgba(0,0,0,0.02)] overflow-hidden transition-all duration-300">

                {{-- Toolbar Filter & Search --}}
                <div class="p-4 border-b border-border space-y-3">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="relative flex-1 min-w-[220px]">
                            <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                            <input id="product-search" type="text"
                                   placeholder="Cari produk, kode produk, atau kategori..."
                                   class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-border bg-surface focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        </div>

                        <button type="button" id="btn-toggle-filters"
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-border text-xs font-medium text-dark hover:bg-gray-50 transition-colors">
                            <i data-lucide="filter" class="w-3.5 h-3.5 text-secondary"></i>
                            <span>Filter</span>
                            <i data-lucide="chevron-down" class="w-3 h-3 text-gray-400"></i>
                        </button>

                        <select id="product-sort"
                                class="text-xs rounded-xl border border-border bg-white px-3 py-2 text-dark focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <option value="code_asc">Kode Produk (A-Z)</option>
                            <option value="code_desc">Kode Produk (Z-A)</option>
                            <option value="stock_desc">Stok Terbanyak</option>
                            <option value="stock_asc">Stok Tersedikit</option>
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

                {{-- Tabel Data Inventory --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr class="text-secondary bg-[#FAFCFF] border-b border-border font-medium text-[11px] uppercase tracking-wider">
                                <th class="pl-4 py-3 w-8">
                                    <input id="check-all" type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary/20">
                                </th>
                                <th class="py-3 pr-3">Kode Produk</th>
                                <th class="py-3 pr-3">Nama Produk</th>
                                <th class="py-3 pr-3">Kategori</th>
                                <th class="py-3 pr-3 text-right">Stok Total</th>
                                <th class="py-3 pr-3 text-right">Min. Stok</th>
                                <th class="py-3 pr-3 text-center">Status</th>
                                <th class="py-3 pr-4 text-center w-8">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="inventory-table-body" class="divide-y divide-gray-100">
                            @foreach ($products as $idx => $item)
                                @php
                                    $isLow = $item['status'] === 'Low Stock';
                                    $isOut = $item['status'] === 'Habis';
                                    $badgeClass = match($item['status']) {
                                        'Aktif'     => 'bg-emerald-50 text-emerald-600 border border-emerald-200/60',
                                        'Low Stock' => 'bg-amber-50 text-amber-600 border border-amber-200/60 font-semibold',
                                        'Habis'     => 'bg-red-50 text-red-600 border border-red-200/60 font-bold',
                                        default     => 'bg-slate-100 text-slate-500 border border-slate-200/60',
                                    };
                                @endphp
                                <tr class="product-row cursor-pointer transition-colors hover:bg-blue-50/40 {{ $idx === 0 ? 'bg-blue-50/70 border-l-4 border-l-primary' : '' }}"
                                    data-id="{{ $item['id'] }}"
                                    data-name="{{ $item['name'] }}"
                                    data-category="{{ $item['category'] }}"
                                    data-stock="{{ $item['total_stock'] }}"
                                    data-min="{{ $item['min_stock'] }}"
                                    data-status="{{ $item['status'] }}"
                                    data-search="{{ strtolower($item['id'] . ' ' . $item['name'] . ' ' . $item['category'] . ' ' . ($item['location'] ?? '')) }}">
                                    <td class="pl-4 py-3">
                                        <input type="checkbox" class="row-checkbox rounded border-gray-300 text-primary focus:ring-primary/20">
                                    </td>
                                    <td class="py-3 pr-3 font-semibold text-primary whitespace-nowrap">
                                        {{ $item['id'] }}
                                    </td>
                                    <td class="py-3 pr-3">
                                        <p class="font-medium text-dark">{{ $item['name'] }}</p>
                                        <p class="text-[11px] text-secondary">{{ $item['unit'] }}</p>
                                    </td>
                                    <td class="py-3 pr-3 whitespace-nowrap text-secondary">
                                        {{ $item['category'] }}
                                    </td>
                                    <td class="py-3 pr-3 text-right font-bold {{ $isLow ? 'text-amber-600' : ($isOut ? 'text-danger' : 'text-dark') }}">
                                        {{ number_format($item['total_stock']) }}
                                    </td>
                                    <td class="py-3 pr-3 text-right text-secondary">
                                        {{ number_format($item['min_stock']) }}
                                    </td>
                                    <td class="py-3 pr-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium whitespace-nowrap {{ $badgeClass }}">
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
                                    Tidak ada produk yang cocok dengan kriteria pencarian / filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Dummy --}}
                <div class="p-3.5 border-t border-border flex flex-wrap items-center justify-between gap-3 text-xs">
                    <p id="table-info" class="text-secondary">Menampilkan 1-10 dari {{ count($products) }} produk</p>
                    <div class="flex items-center gap-1">
                        <button type="button" class="w-7 h-7 rounded-lg border border-border flex items-center justify-center text-secondary hover:bg-gray-50" data-dummy-action="Halaman Sebelumnya">
                            <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
                        </button>
                        <button type="button" class="w-7 h-7 rounded-lg bg-primary text-white font-medium flex items-center justify-center">1</button>
                        <button type="button" class="w-7 h-7 rounded-lg border border-border text-secondary hover:bg-gray-50 flex items-center justify-center" data-dummy-action="Halaman 2">2</button>
                        <button type="button" class="w-7 h-7 rounded-lg border border-border text-secondary hover:bg-gray-50 flex items-center justify-center" data-dummy-action="Halaman 3">3</button>
                        <span class="px-1 text-gray-400">...</span>
                        <button type="button" class="w-7 h-7 rounded-lg border border-border text-secondary hover:bg-gray-50 flex items-center justify-center" data-dummy-action="Halaman 25">25</button>
                        <button type="button" class="w-7 h-7 rounded-lg border border-border flex items-center justify-center text-secondary hover:bg-gray-50" data-dummy-action="Halaman Berikutnya">
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>

            </div>

            {{-- ======================================== --}}
            {{-- KOLOM KANAN: PANEL DETAIL PRODUK         --}}
            {{-- ======================================== --}}
            <aside id="product-detail-panel"
                   class="w-full xl:w-[480px] 2xl:w-[520px] bg-white rounded-2xl border border-border shadow-[0_2px_6px_rgba(0,0,0,0.02)] p-5 shrink-0 transition-all duration-300 xl:sticky xl:top-20">

                {{-- Header Detail Panel --}}
                <div class="flex items-start justify-between pb-4 border-b border-border">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span id="detail-product-id" class="text-lg font-bold text-primary">PRD-001</span>
                            <span id="detail-category-badge" class="px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-blue-600 border border-blue-200/60">Modem/ONT</span>
                            <span id="detail-status-badge" class="px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-600 border border-emerald-200/60">Aktif</span>
                        </div>
                        <p id="detail-product-name" class="text-base font-bold text-dark mt-1">ONT ZTE F670L</p>
                        <p id="detail-product-unit" class="text-xs text-secondary">Satuan: Unit</p>
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
                        <p class="text-[10px] text-secondary uppercase font-semibold">Stok Total</p>
                        <p id="detail-card-stock" class="text-base font-bold text-dark mt-0.5">120</p>
                        <p id="detail-card-unit" class="text-[10px] text-secondary">Unit</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-secondary uppercase font-semibold">Min. Stok</p>
                        <p id="detail-card-min" class="text-base font-bold text-dark mt-0.5">10</p>
                        <p class="text-[10px] text-secondary">Ambang batas</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-secondary uppercase font-semibold">Gudang</p>
                        <p id="detail-card-location" class="font-bold text-dark mt-0.5 truncate">Gudang Utama</p>
                        <p class="text-[10px] text-secondary">Lokasi Utama</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-secondary uppercase font-semibold">Contoh SN</p>
                        <p id="detail-card-serial" class="font-bold text-primary mt-0.5 truncate">ZTE001234</p>
                        <p class="text-[10px] text-secondary">Serial Number</p>
                    </div>
                </div>

                {{-- 4 Tab Navigation --}}
                <div class="flex border-b border-border text-xs font-semibold mb-4 gap-6" id="panel-tabs">
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-primary text-primary transition-colors" data-target="tab-detail">Detail</button>
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-transparent text-secondary hover:text-dark transition-colors" data-target="tab-stock">Stok</button>
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-transparent text-secondary hover:text-dark transition-colors" data-target="tab-movement">Riwayat</button>
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-transparent text-secondary hover:text-dark transition-colors" data-target="tab-related">Terkait</button>
                </div>

                {{-- Konten Tab Panel --}}
                <div class="space-y-4">

                    {{-- TAB 1: DETAIL --}}
                    <div id="tab-detail" class="tab-pane space-y-3 text-xs">
                        <div class="space-y-2">
                            <div>
                                <span class="text-secondary text-[11px] block">Kode Produk:</span>
                                <span id="info-code" class="font-semibold text-dark">PRD-001</span>
                            </div>
                            <div>
                                <span class="text-secondary text-[11px] block">Nama Produk:</span>
                                <span id="info-name" class="font-semibold text-dark">ONT ZTE F670L</span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <span class="text-secondary text-[11px] block">Kategori:</span>
                                    <span id="info-category" class="font-medium text-dark">Modem/ONT</span>
                                </div>
                                <div>
                                    <span class="text-secondary text-[11px] block">Satuan:</span>
                                    <span id="info-unit" class="font-medium text-dark">Unit</span>
                                </div>
                            </div>
                            <div>
                                <span class="text-secondary text-[11px] block">Deskripsi:</span>
                                <p id="info-description" class="text-dark bg-surface p-2.5 rounded-xl border border-border mt-1 leading-relaxed">
                                    Optical Network Terminal (ONT) dual-band GPON dengan 4 port Gigabit Ethernet dan WiFi 2.4/5GHz.
                                </p>
                            </div>
                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div>
                                    <span class="text-secondary text-[11px] block">Lokasi Penyimpanan:</span>
                                    <span id="info-location" class="font-semibold text-dark">Gudang Utama</span>
                                    <p id="info-address" class="text-[11px] text-secondary mt-0.5">Jl. Raya Situbondo No. 12</p>
                                </div>
                                <div>
                                    <span class="text-secondary text-[11px] block">Status Produk:</span>
                                    <span id="info-status-text" class="font-semibold text-emerald-600">Aktif</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 2: STOK --}}
                    <div id="tab-stock" class="tab-pane hidden space-y-2.5 text-xs">
                        <p class="font-semibold text-dark text-xs mb-2">Sebaran Stok Berdasarkan Gudang:</p>
                        <div id="warehouse-stock-list" class="space-y-2">
                            {{-- Diisi secara dinamis oleh JS --}}
                        </div>
                    </div>

                    {{-- TAB 3: RIWAYAT MUTASI --}}
                    <div id="tab-movement" class="tab-pane hidden space-y-3 text-xs">
                        <p class="font-semibold text-dark text-xs mb-2">Riwayat Keluar / Masuk Barang:</p>
                        <div id="movement-list" class="space-y-3">
                            {{-- Diisi secara dinamis oleh JS --}}
                        </div>
                    </div>

                    {{-- TAB 4: TERKAIT --}}
                    <div id="tab-related" class="tab-pane hidden space-y-3 text-xs">
                        <p class="font-semibold text-dark text-xs mb-2">Aset Terpasang & Tiket Terkait:</p>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3 rounded-xl bg-surface border border-border">
                                <p class="text-[11px] text-secondary">Aset Aktif di Customer</p>
                                <p id="related-active-assets" class="text-xl font-bold text-dark mt-1">14 unit</p>
                                <a href="#" class="text-[11px] text-primary font-medium hover:underline mt-1 block" data-dummy-action="Lihat Semua Aset Terpasang">Lihat Daftar Aset →</a>
                            </div>
                            <div class="p-3 rounded-xl bg-surface border border-border">
                                <p class="text-[11px] text-secondary">Tiket Terkait Terbuka</p>
                                <p id="related-open-tickets" class="text-xl font-bold text-amber-600 mt-1">2 tiket</p>
                                <a href="{{ route('tickets.index') }}" class="text-[11px] text-primary font-medium hover:underline mt-1 block">Lihat di Menu Ticket →</a>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Aksi Produk --}}
                    <div class="pt-3 border-t border-border flex items-center gap-2.5">
                        <button type="button" id="btn-edit-product"
                                class="flex-1 py-2 px-3 rounded-xl bg-white border border-border text-dark text-xs font-semibold hover:bg-gray-50 shadow-xs transition-colors flex items-center justify-center gap-1.5">
                            <i data-lucide="edit-3" class="w-3.5 h-3.5 text-secondary"></i>
                            <span>Edit Produk</span>
                        </button>
                        <button type="button" id="btn-delete-product"
                                class="py-2 px-3 rounded-xl bg-white border border-red-200 text-danger text-xs font-semibold hover:bg-red-50 shadow-xs transition-colors flex items-center justify-center gap-1.5">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5 text-danger"></i>
                            <span>Hapus</span>
                        </button>
                    </div>

                </div>

            </aside>

        </div>

    </div>

    {{-- ============================================ --}}
    {{-- 4. MODAL TAMBAH PRODUK                       --}}
    {{-- ============================================ --}}
    <div id="modal-create-product" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs hidden">
        <div class="bg-white rounded-2xl border border-border shadow-2xl max-w-lg w-full overflow-hidden animate-in fade-in duration-150">
            <div class="p-4 border-b border-border flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-dark">Tambah Produk Baru</h3>
                    <p class="text-secondary text-xs">Masukkan data master barang persediaan</p>
                </div>
                <button type="button" id="btn-close-create-modal" class="p-1 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form id="form-create-product" class="p-5 space-y-3.5 text-xs">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1">Kode Produk <span class="text-danger">*</span></label>
                        <input type="text" id="create-code" placeholder="PRD-011" required
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Kategori <span class="text-danger">*</span></label>
                        <select id="create-category" required class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-dark mb-1">Nama Produk <span class="text-danger">*</span></label>
                    <input type="text" id="create-name" placeholder="Contoh: ONT Fiberhome HG6243C" required
                           class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1">Satuan <span class="text-danger">*</span></label>
                        <select id="create-unit" class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                            <option value="Unit">Unit</option>
                            <option value="Pcs">Pcs</option>
                            <option value="Pack">Pack</option>
                            <option value="Roll">Roll</option>
                            <option value="Meter">Meter</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Stok Awal</label>
                        <input type="number" id="create-stock" min="0" value="50" required
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Min. Stok</label>
                        <input type="number" id="create-min" min="0" value="10" required
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-dark mb-1">Gudang Penyimpanan</label>
                    <select id="create-warehouse" class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                        <option value="Gudang Utama">Gudang Utama (Situbondo)</option>
                        <option value="Gudang Situbondo">Gudang Situbondo</option>
                        <option value="Gudang Besuki">Gudang Besuki</option>
                        <option value="Gudang Asembagus">Gudang Asembagus</option>
                    </select>
                </div>

                <div>
                    <label class="block font-medium text-dark mb-1">Deskripsi Produk</label>
                    <textarea id="create-desc" rows="2.5" placeholder="Spesifikasi atau informasi penting produk..."
                              class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
                </div>

                <div class="pt-3 border-t border-border flex items-center justify-end gap-2">
                    <button type="button" id="btn-cancel-create-modal" class="px-3.5 py-2 rounded-xl border border-border text-secondary hover:bg-gray-50 font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-blue-700 shadow-sm">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- 5. MODAL EDIT PRODUK                         --}}
    {{-- ============================================ --}}
    <div id="modal-edit-product" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs hidden">
        <div class="bg-white rounded-2xl border border-border shadow-2xl max-w-lg w-full overflow-hidden animate-in fade-in duration-150">
            <div class="p-4 border-b border-border flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-dark">Edit Data Produk</h3>
                    <p class="text-secondary text-xs">Ubah rincian informasi produk</p>
                </div>
                <button type="button" id="btn-close-edit-modal" class="p-1 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form id="form-edit-product" class="p-5 space-y-3.5 text-xs">
                <input type="hidden" id="edit-id-hidden">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1">Kode Produk</label>
                        <input type="text" id="edit-code" readonly
                               class="w-full rounded-xl border border-border bg-gray-50 text-gray-500 px-3 py-2 text-xs cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Kategori</label>
                        <select id="edit-category" class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-dark mb-1">Nama Produk</label>
                    <input type="text" id="edit-name" required
                           class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1">Satuan</label>
                        <input type="text" id="edit-unit" required
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Stok Total</label>
                        <input type="number" id="edit-stock" min="0" required
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Min. Stok</label>
                        <input type="number" id="edit-min" min="0" required
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
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

    {{-- Toast Notifikasi --}}
    <div id="toast-notification"
         class="fixed bottom-5 right-5 z-50 hidden max-w-sm rounded-xl bg-dark text-white text-xs px-4 py-3 shadow-xl transition-all">
    </div>

    {{-- ============================================ --}}
    {{-- 6. JAVASCRIPT LOGIC INTERAKTIF              --}}
    {{-- ============================================ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Data produk dari Blade
            let productList = @json($products);
            let productMap = {};
            productList.forEach(p => { productMap[p.id] = p; });

            let currentSelectedId = 'PRD-001';

            // Toast helper
            let toastTimer;
            function showToast(message) {
                const toast = document.getElementById('toast-notification');
                toast.textContent = message;
                toast.classList.remove('hidden');
                clearTimeout(toastTimer);
                toastTimer = setTimeout(() => toast.classList.add('hidden'), 2600);
            }

            // Dummy Action Click Handler
            document.querySelectorAll('[data-dummy-action]').forEach(el => {
                el.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    showToast(el.getAttribute('data-dummy-action') + ' (Aksi UI)');
                });
            });

            // 1. Search & Filter & Sort
            const searchInput = document.getElementById('product-search');
            const categoryFilter = document.getElementById('filter-category');
            const statusFilter = document.getElementById('filter-status');
            const sortSelect = document.getElementById('product-sort');
            const rows = Array.from(document.querySelectorAll('.product-row'));
            const tbody = document.getElementById('inventory-table-body');
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
                    if (sortMode === 'stock_desc') return parseInt(b.dataset.stock) - parseInt(a.dataset.stock);
                    if (sortMode === 'stock_asc') return parseInt(a.dataset.stock) - parseInt(b.dataset.stock);
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
                tableInfo.textContent = `Menampilkan ${visibleCount} dari ${rows.length} produk`;
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
            const detailPanel = document.getElementById('product-detail-panel');

            function populateDetail(productId) {
                const p = productMap[productId];
                if (!p) return;
                currentSelectedId = productId;

                // Header
                document.getElementById('detail-product-id').textContent = p.id;
                document.getElementById('detail-product-name').textContent = p.name;
                document.getElementById('detail-product-unit').textContent = `Satuan: ${p.unit}`;
                document.getElementById('detail-category-badge').textContent = p.category;

                const statusBadge = document.getElementById('detail-status-badge');
                statusBadge.textContent = p.status;
                if (p.status === 'Aktif') {
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-600 border border-emerald-200/60';
                } else if (p.status === 'Low Stock') {
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-600 border border-amber-200/60 font-semibold';
                } else if (p.status === 'Habis') {
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-red-50 text-red-600 border border-red-200/60 font-bold';
                } else {
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-500 border border-slate-200/60';
                }

                // 4 Kotak Metrik
                document.getElementById('detail-card-stock').textContent = Number(p.total_stock).toLocaleString('id-ID');
                document.getElementById('detail-card-unit').textContent = p.unit;
                document.getElementById('detail-card-min').textContent = Number(p.min_stock).toLocaleString('id-ID');
                document.getElementById('detail-card-location').textContent = p.location || 'Gudang Utama';
                document.getElementById('detail-card-serial').textContent = p.serial_example || '-';

                // Tab Detail
                document.getElementById('info-code').textContent = p.id;
                document.getElementById('info-name').textContent = p.name;
                document.getElementById('info-category').textContent = p.category;
                document.getElementById('info-unit').textContent = p.unit;
                document.getElementById('info-description').textContent = p.description || 'Tidak ada deskripsi.';
                document.getElementById('info-location').textContent = p.location || 'Gudang Utama';
                document.getElementById('info-address').textContent = p.address || 'Alamat gudang belum diatur.';
                document.getElementById('info-status-text').textContent = p.status;

                // Tab Stok (Per Gudang)
                const whList = document.getElementById('warehouse-stock-list');
                const whData = p.warehouses || [
                    { name: p.location || 'Gudang Utama', stock: p.total_stock, min: p.min_stock }
                ];
                whList.innerHTML = whData.map(wh => `
                    <div class="p-2.5 rounded-xl border border-border bg-surface flex items-center justify-between">
                        <div>
                            <p class="font-medium text-dark">${wh.name}</p>
                            <p class="text-[10px] text-secondary">Min: ${wh.min} ${p.unit}</p>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-dark text-sm">${wh.stock}</span>
                            <span class="text-[10px] text-secondary block">${p.unit}</span>
                        </div>
                    </div>
                `).join('');

                // Tab Riwayat
                const mvList = document.getElementById('movement-list');
                const mvData = p.movements || [];
                mvList.innerHTML = mvData.length ? mvData.map(m => `
                    <div class="p-2.5 rounded-xl border border-border bg-surface flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-1.5">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold ${m.type === 'Masuk' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600'}">${m.type}</span>
                                <span class="font-medium text-dark">${m.qty} ${p.unit}</span>
                            </div>
                            <p class="text-[11px] text-secondary mt-1">${m.ref} • Oleh ${m.user}</p>
                        </div>
                        <span class="text-[10px] text-secondary whitespace-nowrap">${m.date}</span>
                    </div>
                `).join('') : '<p class="text-secondary text-center py-4">Belum ada catatan mutasi.</p>';

                // Tab Terkait
                if (p.related) {
                    document.getElementById('related-active-assets').textContent = `${p.related.active_assets} unit`;
                    document.getElementById('related-open-tickets').textContent = `${p.related.open_tickets} tiket`;
                }

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

            // 4. Modal Tambah Produk
            const createModal = document.getElementById('modal-create-product');
            document.getElementById('btn-open-create-modal').addEventListener('click', () => {
                createModal.classList.remove('hidden');
            });
            const closeCreateModal = () => createModal.classList.add('hidden');
            document.getElementById('btn-close-create-modal').addEventListener('click', closeCreateModal);
            document.getElementById('btn-cancel-create-modal').addEventListener('click', closeCreateModal);

            document.getElementById('form-create-product').addEventListener('submit', (e) => {
                e.preventDefault();
                const code = document.getElementById('create-code').value.trim();
                const name = document.getElementById('create-name').value.trim();
                const category = document.getElementById('create-category').value;
                const unit = document.getElementById('create-unit').value;
                const stock = parseInt(document.getElementById('create-stock').value) || 0;
                const min = parseInt(document.getElementById('create-min').value) || 0;
                const warehouse = document.getElementById('create-warehouse').value;
                const desc = document.getElementById('create-desc').value.trim();

                const status = stock <= 0 ? 'Habis' : (stock <= min ? 'Low Stock' : 'Aktif');

                const newProduct = {
                    id: code,
                    name: name,
                    category: category,
                    unit: unit,
                    total_stock: stock,
                    min_stock: min,
                    status: status,
                    location: warehouse,
                    address: 'Jl. Raya Situbondo',
                    serial_example: 'SN-' + Math.floor(100000 + Math.random() * 900000),
                    description: desc || 'Produk baru ditambahkan ke persediaan.',
                    warehouses: [{ name: warehouse, stock: stock, min: min }],
                    movements: [{
                        date: 'Hari ini',
                        type: 'Masuk',
                        qty: stock,
                        ref: 'Stok Awal Produk Baru',
                        user: 'Administrator'
                    }],
                    related: { active_assets: 0, open_tickets: 0 }
                };

                productMap[code] = newProduct;
                closeCreateModal();
                showToast(`Produk ${code} - ${name} berhasil ditambahkan!`);
                populateDetail(code);
            });

            // 5. Modal Edit Produk
            const editModal = document.getElementById('modal-edit-product');
            document.getElementById('btn-edit-product').addEventListener('click', () => {
                const current = productMap[currentSelectedId];
                if (!current) return;

                document.getElementById('edit-id-hidden').value = current.id;
                document.getElementById('edit-code').value = current.id;
                document.getElementById('edit-name').value = current.name;
                document.getElementById('edit-category').value = current.category;
                document.getElementById('edit-unit').value = current.unit;
                document.getElementById('edit-stock').value = current.total_stock;
                document.getElementById('edit-min').value = current.min_stock;
                document.getElementById('edit-desc').value = current.description;

                editModal.classList.remove('hidden');
            });

            const closeEditModal = () => editModal.classList.add('hidden');
            document.getElementById('btn-close-edit-modal').addEventListener('click', closeEditModal);
            document.getElementById('btn-cancel-edit-modal').addEventListener('click', closeEditModal);

            document.getElementById('form-edit-product').addEventListener('submit', (e) => {
                e.preventDefault();
                const code = document.getElementById('edit-id-hidden').value;
                const current = productMap[code];
                if (!current) return;

                current.name = document.getElementById('edit-name').value.trim();
                current.category = document.getElementById('edit-category').value;
                current.unit = document.getElementById('edit-unit').value.trim();
                current.total_stock = parseInt(document.getElementById('edit-stock').value) || 0;
                current.min_stock = parseInt(document.getElementById('edit-min').value) || 0;
                current.description = document.getElementById('edit-desc').value.trim();
                current.status = current.total_stock <= 0 ? 'Habis' : (current.total_stock <= current.min_stock ? 'Low Stock' : 'Aktif');

                closeEditModal();
                showToast(`Perubahan data ${code} berhasil disimpan!`);
                populateDetail(code);

                // Update row in table
                const row = document.querySelector(`.product-row[data-id="${code}"]`);
                if (row) {
                    row.querySelector('td:nth-child(3) p:first-child').textContent = current.name;
                    row.querySelector('td:nth-child(3) p:last-child').textContent = current.unit;
                    row.querySelector('td:nth-child(4)').textContent = current.category;
                    row.querySelector('td:nth-child(5)').textContent = Number(current.total_stock).toLocaleString('id-ID');
                    row.querySelector('td:nth-child(6)').textContent = Number(current.min_stock).toLocaleString('id-ID');
                    row.dataset.name = current.name;
                    row.dataset.stock = current.total_stock;
                    row.dataset.category = current.category;
                    row.dataset.status = current.status;
                }
            });

            // 6. Hapus Produk
            document.getElementById('btn-delete-product').addEventListener('click', () => {
                const current = productMap[currentSelectedId];
                if (!current) return;

                if (confirm(`Apakah Anda yakin ingin menghapus produk "${current.name}" (${current.id})?`)) {
                    showToast(`Produk ${current.id} berhasil dihapus!`);
                    const row = document.querySelector(`.product-row[data-id="${current.id}"]`);
                    if (row) row.remove();
                    delete productMap[current.id];

                    // Pilih baris pertama yang tersisa
                    const firstRemaining = document.querySelector('.product-row');
                    if (firstRemaining) {
                        firstRemaining.click();
                    } else {
                        detailPanel.classList.add('hidden');
                    }
                }
            });

            // Inisialisasi awal produk pertama
            populateDetail('PRD-001');

            // Refresh icons
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</x-app-layout>
