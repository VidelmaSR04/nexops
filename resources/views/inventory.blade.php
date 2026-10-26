<x-app-layout>

    @php
        // Data berasal dari InventoryController@index. Jika belum ada, tampil kosong (tanpa data dummy).
        $products = $products ?? [];

        $summary = $summary ?? [
            'total_products' => 0,
            'total_stock'    => 0,
            'low_stock'      => 0,
            'warehouse'      => 0,
        ];

        $categories = $categories ?? [];

        $conditions = \App\Models\InventoryItem::CONDITIONS;
        $availabilities = \App\Models\InventoryItem::AVAILABILITIES;

        $productsById = collect($products)->keyBy('id')->all();
        $warehouseOptions = collect($warehouses ?? [])
            ->map(fn ($warehouse) => [
                'id' => $warehouse->id,
                'name' => $warehouse->name,
                'status' => $warehouse->status,
            ])
            ->values();
    @endphp

    <style>
        .product-row.stock-level-menipis:not(.is-selected) { background-color: rgb(255 251 235 / 0.65); }
        .product-row.stock-level-habis:not(.is-selected) { background-color: rgb(254 242 242 / 0.75); }
    </style>

    <div class="space-y-6">

        {{-- ============================================ --}}
        {{-- 1. HEADER INVENTORY                          --}}
        {{-- ============================================ --}}
        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700" role="status">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                <p class="font-semibold">Data belum tersimpan:</p>
                <ul class="mt-1 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-dark tracking-tight">Inventory Peralatan</h1>
                <p class="text-secondary text-sm mt-0.5">Kelola data peralatan kerja teknis dan perlengkapan operasional Kominfo.</p>
            </div>
            <div>
                <button type="button" id="btn-open-create-modal"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-white text-sm font-medium hover:bg-blue-700 shadow-sm transition-colors">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Tambah Peralatan</span>
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
                    <p class="text-xs text-secondary font-medium">Total Jenis Peralatan</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">{{ number_format($summary['total_products']) }}</p>
                </div>
            </div>

            {{-- Total Stok --}}
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center shrink-0">
                    <i data-lucide="boxes" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Total Unit Peralatan</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">{{ number_format($summary['total_stock']) }}</p>
                </div>
            </div>

            {{-- Low Stock --}}
            <div id="low-stock-card" role="button" tabindex="0" class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)] cursor-pointer hover:border-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-300">
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Stok Menipis</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">{{ $summary['low_stock'] }}</p>
                    @if (($summary['out_of_stock'] ?? 0) > 0)
                        <p class="text-[10px] text-secondary">{{ $summary['out_of_stock'] }} di antaranya habis</p>
                    @endif
                </div>
            </div>

            {{-- Warehouse --}}
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i data-lucide="warehouse" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Lokasi Penyimpanan</p>
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
                                   placeholder="Cari peralatan, kode inventaris, atau kategori..."
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
                            <option value="code_asc">Kode Inventaris (A-Z)</option>
                            <option value="code_desc">Kode Inventaris (Z-A)</option>
                            <option value="stock_desc">Jumlah Terbanyak</option>
                            <option value="stock_asc">Jumlah Tersedikit</option>
                            <option value="restock_first">Perlu restock dulu</option>
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
                            <option value="">Semua Ketersediaan</option>
                            @foreach ($availabilities as $availability)
                                <option value="{{ $availability }}">{{ $availability }}</option>
                            @endforeach
                        </select>

                        <select id="filter-stock-level" class="text-xs rounded-lg border border-border bg-white px-2.5 py-1.5 text-dark">
                            <option value="">Semua Level Stok</option>
                            <option value="Perlu Restock">Perlu Restock (Menipis &amp; Habis)</option>
                            <option value="Menipis">Menipis</option>
                            <option value="Habis">Habis</option>
                        </select>

                        <button type="button" id="btn-reset-filters"
                                class="text-xs rounded-lg border border-border px-3 py-1.5 text-secondary hover:bg-gray-50 text-center font-medium">
                            Reset Filter
                        </button>
                    </div>
                </div>

                <div id="active-filter-chip" class="hidden items-center gap-2 border-t border-border bg-blue-50/60 px-4 py-2 text-[11px] text-secondary">
                    <span>Filter aktif:</span>
                    <span id="active-filter-summary" class="font-semibold text-dark"></span>
                    <button type="button" id="btn-clear-active-filters" class="ml-auto inline-flex items-center gap-1 rounded-lg px-2 py-1 font-medium text-primary hover:bg-white">
                        <span aria-hidden="true">✕</span>
                        <span>Tampilkan semua</span>
                    </button>
                </div>

                {{-- Tabel Data Inventory --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr class="text-secondary bg-[#FAFCFF] border-b border-border font-medium text-[11px] uppercase tracking-wider">
                                <th class="pl-4 py-3 w-8">
                                    <input id="check-all" type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary/20">
                                </th>
                                <th class="py-3 pr-3 w-14">Foto</th>
                                <th class="py-3 pr-3 whitespace-nowrap">Kode Inventaris</th>
                                <th class="py-3 pr-3 min-w-[160px]">Nama Peralatan</th>
                                <th class="py-3 pr-3 whitespace-nowrap">Kategori</th>
                                <th class="py-3 pr-3 text-right">Jumlah</th>
                                <th class="py-3 pr-3 text-center">Kondisi</th>
                                <th class="py-3 pr-3 text-center whitespace-nowrap">Status Ketersediaan</th>
                                <th class="py-3 pr-3 whitespace-nowrap">Lokasi</th>
                                <th class="py-3 pr-4 text-center w-8">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="inventory-table-body" class="divide-y divide-gray-100">
                            @foreach ($products as $idx => $item)
                                @php
                                    $condition    = $item['condition'];
                                    $availability = $item['availability'];
                                    $isLow = $item['status'] === 'Low Stock';
                                    $isOut = $item['status'] === 'Habis';
                                    $stockLevel = $item['stock_level'] ?? 'Aman';
                                    $stockBadgeClass = match ($stockLevel) {
                                        'Menipis' => 'bg-amber-50 text-amber-700 border border-amber-200',
                                        'Habis' => 'bg-red-50 text-red-700 border border-red-200',
                                        default => '',
                                    };

                                    $conditionClass = match($condition) {
                                        'Baru', 'Baik'          => 'bg-emerald-50 text-emerald-600 border border-emerald-200/60',
                                        'Perlu Perbaikan'       => 'bg-amber-50 text-amber-600 border border-amber-200/60',
                                        'Rusak'                 => 'bg-red-50 text-red-600 border border-red-200/60',
                                        default                 => 'bg-slate-100 text-slate-500 border border-slate-200/60',
                                    };
                                    $badgeClass = match($availability) {
                                        'Tersedia'                => 'bg-emerald-50 text-emerald-600 border border-emerald-200/60',
                                        'Sedang Digunakan'        => 'bg-purple-50 text-purple-600 border border-purple-200/60',
                                        'Sedang Diperbaiki'       => 'bg-amber-50 text-amber-600 border border-amber-200/60',
                                        'Hilang'                  => 'bg-red-50 text-red-600 border border-red-200/60',
                                        default                   => 'bg-slate-100 text-slate-500 border border-slate-200/60',
                                    };
                                    $warehouseBreakdown = collect($item['warehouses'] ?? [])
                                        ->filter(fn ($warehouse) => (int) ($warehouse['stock'] ?? 0) > 0)
                                        ->map(fn ($warehouse) => ($warehouse['name'] ?? '-') . ': ' . number_format((int) $warehouse['stock']))
                                        ->implode(' · ');
                                    $warehouseBreakdown = $warehouseBreakdown ?: 'Belum ada stok di gudang mana pun';
                                @endphp
                                <tr class="product-row cursor-pointer transition-colors hover:bg-blue-50/40 {{ $idx === 0 ? 'is-selected bg-blue-50/70 border-l-4 border-l-primary' : '' }} {{ $stockLevel === 'Menipis' ? 'stock-level-menipis' : ($stockLevel === 'Habis' ? 'stock-level-habis' : '') }}"
                                    data-id="{{ $item['id'] }}"
                                    data-name="{{ $item['name'] }}"
                                    data-category="{{ $item['category'] }}"
                                    data-stock="{{ $item['total_stock'] }}"
                                    data-min="{{ $item['min_stock'] }}"
                                    data-status="{{ $item['status'] }}"
                                    data-stock-level="{{ $stockLevel }}"
                                    data-availability="{{ $availability }}"
                                    data-search="{{ strtolower($item['id'] . ' ' . $item['name'] . ' ' . ($item['brand'] ?? '') . ' ' . $item['category'] . ' ' . ($item['location_label'] ?? $item['location'] ?? '')) }}">
                                    <td class="pl-4 py-3">
                                        <input type="checkbox" class="row-checkbox rounded border-gray-300 text-primary focus:ring-primary/20">
                                    </td>
                                    <td class="py-3 pr-3">
                                        @if (! empty($item['photo']))
                                            <img src="{{ $item['photo'] }}" alt="{{ $item['name'] }}" loading="lazy" class="w-10 h-10 rounded-lg object-cover border border-border bg-gray-50">
                                        @else
                                            <div class="w-10 h-10 rounded-lg border border-border bg-blue-50 text-primary flex items-center justify-center">
                                                <i data-lucide="wrench" class="w-4 h-4"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3 pr-3 font-semibold text-primary whitespace-nowrap">
                                        {{ $item['id'] }}
                                    </td>
                                    <td class="py-3 pr-3">
                                        <p class="font-medium text-dark">{{ $item['name'] }}</p>
                                        <p class="text-[11px] text-secondary">{{ $item['brand'] ?? '-' }}</p>
                                    </td>
                                    <td class="py-3 pr-3 whitespace-nowrap text-secondary">
                                        {{ $item['category'] }}
                                    </td>
                                    <td class="py-3 pr-3 text-right font-bold whitespace-nowrap {{ $isLow ? 'text-amber-600' : ($isOut ? 'text-danger' : 'text-dark') }}"
                                        title="Stok {{ $item['total_stock'] }}, minimum {{ $item['min_stock'] }}, kurang {{ $item['restock_needed'] ?? 0 }}">
                                        {{ number_format($item['total_stock']) }}
                                        <span class="font-normal text-[11px] text-secondary">{{ $item['unit'] }}</span>
                                        @if ($stockLevel !== 'Aman')
                                            <span class="block mt-1">
                                                <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $stockBadgeClass }}">{{ $stockLevel }}</span>
                                            </span>
                                            <span class="block text-[10px] font-normal text-secondary">Min: {{ number_format($item['min_stock']) }} {{ $item['unit'] }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 pr-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium whitespace-nowrap {{ $conditionClass }}">
                                            {{ $condition }}
                                        </span>
                                    </td>
                                    <td class="py-3 pr-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium whitespace-nowrap {{ $badgeClass }}">
                                            {{ $availability }}
                                        </span>
                                    </td>
                                    <td class="py-3 pr-3 text-secondary" title="{{ $warehouseBreakdown }}">
                                        {{ $item['location_label'] }}
                                    </td>
                                    <td class="py-3 pr-4 text-center">
                                        <button type="button" class="p-1 rounded-md text-gray-400 hover:text-dark hover:bg-gray-100" data-product-action="edit" aria-label="Edit {{ $item['name'] }}">
                                            <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                            <tr id="empty-state-row" class="{{ count($products) ? 'hidden' : '' }}">
                                <td colspan="10" class="py-12 text-center text-gray-400">
                                    <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                                    <p>Tidak ada peralatan yang cocok</p>
                                    <button type="button" id="btn-empty-show-all" class="mt-2 font-medium text-primary underline underline-offset-2 hover:no-underline">
                                        Tampilkan semua
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="p-3.5 border-t border-border flex flex-wrap items-center justify-between gap-3 text-xs">
                    <p id="table-info" class="text-secondary">Menampilkan {{ count($products) ? '1-' . count($products) : '0' }} dari {{ count($products) }} peralatan</p>
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
                            <span id="detail-product-id" class="text-lg font-bold text-primary">-</span>
                            <span id="detail-category-badge" class="px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-blue-600 border border-blue-200/60">Modem/ONT</span>
                            <span id="detail-status-badge" class="px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-600 border border-emerald-200/60">Tersedia</span>
                        </div>
                        <p id="detail-product-name" class="text-base font-bold text-dark mt-1">Pilih peralatan</p>
                        <p id="detail-product-unit" class="text-xs text-secondary">Satuan: Unit</p>
                    </div>

                    <button type="button" id="btn-close-detail"
                            class="p-1.5 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100 transition-colors"
                            title="Tutup Panel Detail">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <div id="detail-stock-alert" role="status" class="hidden mt-4 rounded-xl border px-3 py-2 text-xs font-medium"></div>

                {{-- Kartu Metrik Ringkas 4 Kolom --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 p-3 rounded-xl bg-surface border border-border/80 text-xs my-4">
                    <div>
                        <p class="text-[10px] text-secondary uppercase font-semibold">Total Stok</p>
                        <p id="detail-card-stock" class="text-base font-bold text-dark mt-0.5">-</p>
                        <p id="detail-card-unit" class="text-[10px] text-secondary">Semua lokasi</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-secondary uppercase font-semibold">Batas Minimum</p>
                        <p id="detail-card-min" class="text-base font-bold text-dark mt-0.5">-</p>
                        <p class="text-[10px] text-secondary">Ambang batas</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-secondary uppercase font-semibold">Lokasi</p>
                        <p id="detail-card-location" class="font-bold text-dark mt-0.5 truncate">-</p>
                        <p class="text-[10px] text-secondary">Lokasi dengan stok terbanyak</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-secondary uppercase font-semibold">Contoh SN</p>
                        <p id="detail-card-serial" class="font-bold text-primary mt-0.5 truncate">-</p>
                        <p class="text-[10px] text-secondary">Serial Number</p>
                    </div>
                </div>

                {{-- 4 Tab Navigation --}}
                <div class="flex border-b border-border text-xs font-semibold mb-4 gap-6" id="panel-tabs">
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-primary text-primary transition-colors" data-target="tab-detail">Detail</button>
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-transparent text-secondary hover:text-dark transition-colors" data-target="tab-stock">Lokasi & Jumlah</button>
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-transparent text-secondary hover:text-dark transition-colors" data-target="tab-movement">Riwayat Penggunaan</button>
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-transparent text-secondary hover:text-dark transition-colors" data-target="tab-related">Terkait</button>
                </div>

                {{-- Konten Tab Panel --}}
                <div class="space-y-4">

                    {{-- TAB 1: DETAIL --}}
                    <div id="tab-detail" class="tab-pane space-y-3 text-xs">
                        <div class="space-y-2">
                            <div>
                                <span class="text-secondary text-[11px] block">Kode Inventaris:</span>
                                <span id="info-code" class="font-semibold text-dark">-</span>
                            </div>
                            <div>
                                <span class="text-secondary text-[11px] block">Nama Peralatan:</span>
                                <span id="info-name" class="font-semibold text-dark">-</span>
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
                                    <span id="info-location" class="font-semibold text-dark">-</span>
                                    <p id="info-address" class="text-[11px] text-secondary mt-0.5">-</p>
                                </div>
                                <div>
                                    <span class="text-secondary text-[11px] block">Kondisi:</span>
                                    <span id="info-condition-text" class="font-semibold text-emerald-600">-</span>
                                </div>
                                <div>
                                    <span class="text-secondary text-[11px] block">Status Ketersediaan:</span>
                                    <span id="info-status-text" class="font-semibold text-emerald-600">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 2: STOK --}}
                    <div id="tab-stock" class="tab-pane hidden space-y-2.5 text-xs">
                        <p class="font-semibold text-dark text-xs mb-2">Sebaran Unit Berdasarkan Lokasi Penyimpanan:</p>
                        <div id="warehouse-stock-list" class="space-y-2">
                            {{-- Diisi secara dinamis oleh JS --}}
                        </div>
                    </div>

                    {{-- TAB 3: RIWAYAT MUTASI --}}
                    <div id="tab-movement" class="tab-pane hidden space-y-3 text-xs">
                        <p class="font-semibold text-dark text-xs mb-2">Riwayat Penggunaan Peralatan:</p>
                        <div id="movement-list" class="space-y-3">
                            {{-- Diisi secara dinamis oleh JS --}}
                        </div>
                    </div>

                    {{-- TAB 4: TERKAIT --}}
                    <div id="tab-related" class="tab-pane hidden space-y-3 text-xs">
                        <p class="font-semibold text-dark text-xs mb-2">Aset & Tiket Terkait:</p>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3 rounded-xl bg-surface border border-border">
                                <p class="text-[11px] text-secondary">Aset Terkait</p>
                                <p id="related-active-assets" class="text-xl font-bold text-dark mt-1">-</p>
                                <a href="#" class="text-[11px] text-primary font-medium hover:underline mt-1 block" data-dummy-action="Lihat Semua Aset Terpasang">Lihat Daftar Aset →</a>
                            </div>
                            <div class="p-3 rounded-xl bg-surface border border-border">
                                <p class="text-[11px] text-secondary">Tiket Terkait Terbuka</p>
                                <p id="related-open-tickets" class="text-xl font-bold text-amber-600 mt-1">-</p>
                                <a href="{{ route('tickets.index') }}" class="text-[11px] text-primary font-medium hover:underline mt-1 block">Lihat di Menu Ticket →</a>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Aksi Peralatan --}}
                    <div class="pt-3 border-t border-border flex items-center gap-2.5">
                        <button type="button" id="btn-edit-product"
                                class="flex-1 py-2 px-3 rounded-xl bg-white border border-border text-dark text-xs font-semibold hover:bg-gray-50 shadow-xs transition-colors flex items-center justify-center gap-1.5">
                            <i data-lucide="edit-3" class="w-3.5 h-3.5 text-secondary"></i>
                            <span>Edit Peralatan</span>
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
                    <h3 class="text-base font-bold text-dark">Tambah Peralatan Baru</h3>
                    <p class="text-secondary text-xs">Masukkan data peralatan kerja teknis</p>
                </div>
                <button type="button" id="btn-close-create-modal" class="p-1 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form id="form-create-product" action="{{ route('inventory.store') }}" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1">Kode Inventaris</label>
                        <input type="text" id="create-code" value="{{ $skuPreview ?? 'PRD-001' }}" readonly
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs bg-gray-50 text-gray-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Kategori <span class="text-danger">*</span></label>
                        <input type="text" id="create-category" name="category" list="inventory-categories" required
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                        <datalist id="inventory-categories">
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}">
                            @endforeach
                        </datalist>
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-dark mb-1">Nama Peralatan <span class="text-danger">*</span></label>
                    <input type="text" id="create-name" placeholder="Contoh: ONT Fiberhome HG6243C" required
                           class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1">Satuan <span class="text-danger">*</span></label>
                        <select id="create-unit" name="unit" required class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                            <option value="Unit">Unit</option>
                            <option value="Pcs">Pcs</option>
                            <option value="Pack">Pack</option>
                            <option value="Roll">Roll</option>
                            <option value="Meter">Meter</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Jumlah Awal</label>
                        <input type="number" id="create-stock" min="0" value="50" required
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Batas Minimum</label>
                        <input type="number" id="create-min" min="0" value="10" required
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="create-condition" class="block font-medium text-dark mb-1">Kondisi</label>
                        <select id="create-condition" required class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                            @foreach ($conditions as $condition)
                                <option value="{{ $condition }}" @selected($condition === 'Baik')>{{ $condition }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="create-availability" class="block font-medium text-dark mb-1">Status Ketersediaan</label>
                        <select id="create-availability" required class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                            @foreach ($availabilities as $availability)
                                <option value="{{ $availability }}" @selected($availability === 'Tersedia')>{{ $availability }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-dark mb-1">Lokasi Penyimpanan</label>
                    <select id="create-warehouse" class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                        <option value="">Pilih lokasi penyimpanan</option>
                        {{-- Kirim $locations dari controller (mis. Warehouse::pluck('name')) --}}
                        @foreach (($locations ?? []) as $loc)
                            <option value="{{ $loc }}">{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-medium text-dark mb-1">Catatan Peralatan</label>
                    <textarea id="create-desc" rows="2.5" placeholder="Catatan, merek/tipe, atau informasi penting peralatan..."
                              class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
                </div>

                <div>
                    <label class="block font-medium text-dark mb-1">Contoh Serial Number</label>
                    <input type="text" id="create-serial-example" maxlength="80"
                           placeholder="Contoh: ZTE001234"
                           class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>

                <div class="pt-3 border-t border-border flex items-center justify-end gap-2">
                    <button type="button" id="btn-cancel-create-modal" class="px-3.5 py-2 rounded-xl border border-border text-secondary hover:bg-gray-50 font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-blue-700 shadow-sm">Simpan Peralatan</button>
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
                    <h3 class="text-base font-bold text-dark">Edit Data Peralatan</h3>
                    <p class="text-secondary text-xs">Ubah rincian informasi peralatan</p>
                </div>
                <button type="button" id="btn-close-edit-modal" class="p-1 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form id="form-edit-product" method="POST" class="p-5 space-y-3.5 text-xs max-h-[75vh] overflow-y-auto">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit-id-hidden">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1">Kode Inventaris</label>
                        <input type="text" id="edit-code" readonly
                               class="w-full rounded-xl border border-border bg-gray-50 text-gray-500 px-3 py-2 text-xs cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Kategori</label>
                        <input type="text" id="edit-category" name="category" list="inventory-categories" required
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-dark mb-1">Nama Peralatan</label>
                    <input type="text" id="edit-name" required
                           class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1">Satuan</label>
                        <input type="text" id="edit-unit" name="unit" required
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Batas Minimum</label>
                        <input type="number" id="edit-min" min="0" required
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div class="col-span-2">
                        <p class="block font-medium text-dark mb-2">Stok per Gudang</p>
                        <div id="edit-warehouse-stocks" class="space-y-2"></div>
                        <p id="edit-stock-total" class="mt-2 text-[10px] text-secondary">Total: 0 Unit</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="edit-condition" class="block font-medium text-dark mb-1">Kondisi</label>
                        <select id="edit-condition" required class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                            @foreach ($conditions as $condition)
                                <option value="{{ $condition }}">{{ $condition }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="edit-availability" class="block font-medium text-dark mb-1">Status Ketersediaan</label>
                        <select id="edit-availability" required class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                            @foreach ($availabilities as $availability)
                                <option value="{{ $availability }}">{{ $availability }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-dark mb-1">Deskripsi</label>
                    <textarea id="edit-desc" name="description" rows="2"
                              class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
                </div>

                <div>
                    <label class="block font-medium text-dark mb-1">Contoh Serial Number</label>
                    <input type="text" id="edit-serial-example" maxlength="80"
                           placeholder="Contoh: ZTE001234"
                           class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
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
    <form id="form-delete-product" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    {{-- ============================================ --}}
    {{-- 6. JAVASCRIPT LOGIC INTERAKTIF              --}}
    {{-- ============================================ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Data produk dari Blade
            let productList = @json($products);
            const warehouseList = @json($warehouseOptions);
            const inventoryStoreUrl = @json(route('inventory.store'));
            const inventoryUpdateUrl = @json(route('inventory.update', ['id' => '__ID__']));
            const inventoryDestroyUrl = @json(route('inventory.destroy', ['id' => '__ID__']));
            let productMap = {};
            productList.forEach(p => { productMap[p.id] = p; });

            let currentSelectedId = productList.length ? productList[0].id : null;

            // Toast helper
            let toastTimer;
            function showToast(message) {
                const toast = document.getElementById('toast-notification');
                toast.textContent = message;
                toast.classList.remove('hidden');
                clearTimeout(toastTimer);
                toastTimer = setTimeout(() => toast.classList.add('hidden'), 2600);
            }

            async function sendInventoryRequest(url, method, payload = null) {
                const response = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    ...(payload ? { body: JSON.stringify(payload) } : {}),
                });
                const data = await response.json();

                if (!response.ok) {
                    const validationErrors = Object.values(data.errors || {}).flat();
                    throw new Error(validationErrors.join(' ') || data.message || 'Permintaan inventori gagal.');
                }

                return data;
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
            const availabilityFilter = document.getElementById('filter-status');
            const stockLevelFilter = document.getElementById('filter-stock-level');
            const sortSelect = document.getElementById('product-sort');
            const rows = Array.from(document.querySelectorAll('.product-row'));
            const tbody = document.getElementById('inventory-table-body');
            const emptyState = document.getElementById('empty-state-row');
            const tableInfo = document.getElementById('table-info');
            const activeFilterChip = document.getElementById('active-filter-chip');
            const activeFilterSummary = document.getElementById('active-filter-summary');

            function resetFilters() {
                searchInput.value = '';
                categoryFilter.value = '';
                availabilityFilter.value = '';
                stockLevelFilter.value = '';
                sortSelect.value = 'code_asc';
                applyFilters();
            }

            function applyFilters() {
                const q = searchInput.value.toLowerCase().trim();
                const cat = categoryFilter.value;
                const availability = availabilityFilter.value;
                const stockLevel = stockLevelFilter.value;
                const sortMode = sortSelect.value;
                const activeFilters = [];

                if (q) activeFilters.push(`Pencarian: ${searchInput.value.trim()}`);
                if (cat) activeFilters.push(`Kategori: ${categoryFilter.options[categoryFilter.selectedIndex].text}`);
                if (availability) activeFilters.push(`Ketersediaan: ${availabilityFilter.options[availabilityFilter.selectedIndex].text}`);
                if (stockLevel) activeFilters.push(stockLevel === 'Perlu Restock' ? 'Perlu Restock' : stockLevel);

                activeFilterSummary.textContent = activeFilters.join(' · ');
                activeFilterChip.classList.toggle('hidden', activeFilters.length === 0);
                activeFilterChip.classList.toggle('flex', activeFilters.length > 0);

                // Sorting rows
                const sortedRows = rows.slice().sort((a, b) => {
                    if (sortMode === 'code_asc') return a.dataset.id.localeCompare(b.dataset.id);
                    if (sortMode === 'code_desc') return b.dataset.id.localeCompare(a.dataset.id);
                    if (sortMode === 'stock_desc') return parseInt(b.dataset.stock) - parseInt(a.dataset.stock);
                    if (sortMode === 'stock_asc') return parseInt(a.dataset.stock) - parseInt(b.dataset.stock);
                    if (sortMode === 'restock_first') {
                        const levelOrder = { Habis: 0, Menipis: 1, Aman: 2 };
                        const levelDifference = levelOrder[a.dataset.stockLevel] - levelOrder[b.dataset.stockLevel];
                        return levelDifference || a.dataset.id.localeCompare(b.dataset.id);
                    }
                    return 0;
                });

                sortedRows.forEach(r => tbody.insertBefore(r, emptyState));

                let visibleCount = 0;
                sortedRows.forEach(r => {
                    const matchQ = !q || r.dataset.search.includes(q);
                    const matchCat = !cat || r.dataset.category === cat;
                    const matchAvailability = !availability || r.dataset.availability === availability;
                    const matchStockLevel = stockLevel === 'Perlu Restock'
                        ? ['Menipis', 'Habis'].includes(r.dataset.stockLevel)
                        : !stockLevel || r.dataset.stockLevel === stockLevel;

                    const match = matchQ && matchCat && matchAvailability && matchStockLevel;
                    r.classList.toggle('hidden', !match);
                    if (match) visibleCount++;
                });

                emptyState.classList.toggle('hidden', visibleCount > 0);
                tableInfo.textContent = `Menampilkan ${visibleCount ? `1-${visibleCount}` : '0'} dari ${rows.length} peralatan`;
            }

            [searchInput, categoryFilter, availabilityFilter, stockLevelFilter, sortSelect].forEach(el => {
                el.addEventListener(el === searchInput ? 'input' : 'change', applyFilters);
            });
            applyFilters();

            const showRestockItems = () => {
                stockLevelFilter.value = 'Perlu Restock';
                if (filterDropdown.classList.contains('hidden')) {
                    filterDropdown.classList.remove('hidden');
                    filterDropdown.classList.add('grid');
                }
                applyFilters();
            };
            const lowStockCard = document.getElementById('low-stock-card');
            lowStockCard.addEventListener('click', showRestockItems);
            lowStockCard.addEventListener('keydown', event => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    showRestockItems();
                }
            });
            // Toggle Filter Box
            const toggleFiltersBtn = document.getElementById('btn-toggle-filters');
            const filterDropdown = document.getElementById('filter-dropdown-panel');
            toggleFiltersBtn.addEventListener('click', () => {
                filterDropdown.classList.toggle('hidden');
                filterDropdown.classList.toggle('grid');
            });

            document.getElementById('btn-reset-filters').addEventListener('click', resetFilters);
            document.getElementById('btn-clear-active-filters').addEventListener('click', resetFilters);
            document.getElementById('btn-empty-show-all').addEventListener('click', resetFilters);

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
                const availabilityClasses = {
                    'Tersedia': 'bg-emerald-50 text-emerald-600 border border-emerald-200/60',
                    'Sedang Digunakan': 'bg-purple-50 text-purple-600 border border-purple-200/60',
                    'Sedang Diperbaiki': 'bg-amber-50 text-amber-600 border border-amber-200/60',
                    'Hilang': 'bg-red-50 text-red-600 border border-red-200/60',
                };
                statusBadge.textContent = p.availability;
                statusBadge.className = `px-2.5 py-0.5 rounded-full text-[11px] font-medium ${availabilityClasses[p.availability] || 'bg-slate-100 text-slate-500 border border-slate-200/60'}`;

                // 4 Kotak Metrik
                document.getElementById('detail-card-stock').textContent = Number(p.total_stock).toLocaleString('id-ID');
                document.getElementById('detail-card-unit').textContent = 'Semua lokasi';
                document.getElementById('detail-card-min').textContent = Number(p.min_stock).toLocaleString('id-ID');
                document.getElementById('detail-card-location').textContent = p.location_label || '-';
                document.getElementById('detail-card-serial').textContent = p.serial_example || '-';
                const stockAlert = document.getElementById('detail-stock-alert');
                const stockCardValue = document.getElementById('detail-card-stock');
                stockCardValue.classList.remove('text-dark', 'text-amber-700', 'text-red-700');
                stockAlert.classList.remove('hidden', 'border-amber-200', 'bg-amber-50', 'text-amber-800', 'border-red-200', 'bg-red-50', 'text-red-800');
                if (p.stock_level === 'Habis') {
                    stockAlert.textContent = 'Stok habis';
                    stockAlert.classList.add('border-red-200', 'bg-red-50', 'text-red-800');
                    stockCardValue.classList.add('text-red-700');
                } else if (p.stock_level === 'Menipis') {
                    stockAlert.textContent = `Stok menipis: ${Number(p.total_stock).toLocaleString('id-ID')} ${p.unit} dari minimum ${Number(p.min_stock).toLocaleString('id-ID')}, segera restock`;
                    stockAlert.classList.add('border-amber-200', 'bg-amber-50', 'text-amber-800');
                    stockCardValue.classList.add('text-amber-700');
                } else {
                    stockAlert.classList.add('hidden');
                    stockCardValue.classList.add('text-dark');
                }

                // Tab Detail
                document.getElementById('info-code').textContent = p.id;
                document.getElementById('info-name').textContent = p.name;
                document.getElementById('info-category').textContent = p.category;
                document.getElementById('info-unit').textContent = p.unit;
                document.getElementById('info-description').textContent = p.description || 'Tidak ada deskripsi.';
                document.getElementById('info-location').textContent = p.location || '-';
                document.getElementById('info-address').textContent = p.address || 'Alamat gudang belum diatur.';
                const conditionText = document.getElementById('info-condition-text');
                const conditionTextClasses = {
                    'Baru': 'text-emerald-600',
                    'Baik': 'text-emerald-600',
                    'Perlu Perbaikan': 'text-amber-600',
                    'Rusak': 'text-red-600',
                };
                conditionText.textContent = p.condition;
                conditionText.className = `font-semibold ${conditionTextClasses[p.condition] || 'text-slate-500'}`;

                const availabilityText = document.getElementById('info-status-text');
                const availabilityTextClasses = {
                    'Tersedia': 'text-emerald-600',
                    'Sedang Digunakan': 'text-purple-600',
                    'Sedang Diperbaiki': 'text-amber-600',
                    'Hilang': 'text-red-600',
                };
                availabilityText.textContent = p.availability;
                availabilityText.className = `font-semibold ${availabilityTextClasses[p.availability] || 'text-slate-500'}`;

                // Tab Stok (Per Gudang)
                const whList = document.getElementById('warehouse-stock-list');
                const whData = p.warehouses || [
                    { name: p.location || '-', stock: p.total_stock, min: p.min_stock }
                ];
                whList.innerHTML = whData.map(wh => `
                    <div class="p-2.5 rounded-xl border border-border bg-surface flex items-center justify-between">
                        <div>
                            <p class="font-medium text-dark">${wh.name}</p>
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

            function selectProduct(productId) {
                const selectedRow = rows.find(row => row.dataset.id === productId);
                if (!selectedRow) return;

                rows.forEach(row => {
                    row.classList.remove('is-selected', 'bg-blue-50/70', 'border-l-4', 'border-l-primary');
                });
                selectedRow.classList.add('is-selected', 'bg-blue-50/70', 'border-l-4', 'border-l-primary');
                populateDetail(productId);
                detailPanel.classList.remove('hidden');
            }

            // Click Row Event
            rows.forEach(r => {
                r.addEventListener('click', (e) => {
                    if (e.target.closest('input, button')) return;
                    selectProduct(r.dataset.id);
                });
            });

            document.querySelectorAll('[data-product-action="edit"]').forEach(button => {
                button.addEventListener('click', () => {
                    button.closest('.product-row')?.click();
                    document.getElementById('btn-edit-product').click();
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

            document.getElementById('form-create-product').addEventListener('submit', async (e) => {
                e.preventDefault();
                const form = e.currentTarget;
                const submitButton = form.querySelector('[type="submit"]');
                submitButton.disabled = true;

                try {
                    const data = await sendInventoryRequest(inventoryStoreUrl, 'POST', {
                        sku: document.getElementById('create-code').value.trim(),
                        name: document.getElementById('create-name').value.trim(),
                        category: document.getElementById('create-category').value,
                        unit: document.getElementById('create-unit').value,
                        quantity: parseInt(document.getElementById('create-stock').value) || 0,
                        min_stock: parseInt(document.getElementById('create-min').value) || 0,
                        warehouse: document.getElementById('create-warehouse').value,
                        condition: document.getElementById('create-condition').value,
                        availability: document.getElementById('create-availability').value,
                        description: document.getElementById('create-desc').value.trim(),
                        serial_example: document.getElementById('create-serial-example').value.trim() || null,
                    });

                    showToast(data.message || 'Peralatan berhasil ditambahkan.');
                    setTimeout(() => window.location.reload(), 500);
                } catch (error) {
                    showToast(error.message || 'Gagal menyimpan peralatan.');
                    submitButton.disabled = false;
                }
            });

            // 5. Modal Edit Produk
            const editModal = document.getElementById('modal-edit-product');
            const editForm = document.getElementById('form-edit-product');
            const editStockTotal = document.getElementById('edit-stock-total');
            const editWarehouseStocks = document.getElementById('edit-warehouse-stocks');

            function updateEditStockTotal(unit) {
                const total = Array.from(editWarehouseStocks.querySelectorAll('.edit-warehouse-stock'))
                    .reduce((sum, input) => sum + (Number.parseInt(input.value, 10) || 0), 0);
                editStockTotal.textContent = `Total: ${total.toLocaleString('id-ID')} ${unit}`;
            }

            function renderEditWarehouseStocks(current) {
                const stockByWarehouse = new Map(
                    (current.warehouses || []).map(stock => [Number(stock.id), Number(stock.stock)])
                );
                editWarehouseStocks.replaceChildren();

                warehouseList.forEach(warehouse => {
                    const row = document.createElement('div');
                    row.className = 'flex items-center justify-between gap-3';

                    const label = document.createElement('label');
                    label.htmlFor = `edit-stock-${warehouse.id}`;
                    label.className = 'text-xs text-dark';
                    label.textContent = warehouse.name;

                    const input = document.createElement('input');
                    input.id = `edit-stock-${warehouse.id}`;
                    input.type = 'number';
                    input.min = '0';
                    input.value = stockByWarehouse.get(Number(warehouse.id)) || 0;
                    input.dataset.warehouseId = warehouse.id;
                    input.className = 'edit-warehouse-stock w-28 rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary disabled:bg-gray-100 disabled:text-gray-500';
                    input.disabled = warehouse.status === 'Nonaktif';
                    input.setAttribute('aria-label', `Stok ${warehouse.name}`);
                    input.addEventListener('input', () => updateEditStockTotal(current.unit));
                    row.append(label, input);
                    editWarehouseStocks.append(row);
                });

                updateEditStockTotal(current.unit);
            }

            document.getElementById('btn-edit-product').addEventListener('click', () => {
                const current = productMap[currentSelectedId];
                if (!current) return;

                document.getElementById('edit-id-hidden').value = current.id;
                document.getElementById('edit-code').value = current.id;
                editForm.action = `{{ url('/inventory') }}/${encodeURIComponent(current.id)}`;
                document.getElementById('edit-name').value = current.name;
                document.getElementById('edit-category').value = current.category;
                document.getElementById('edit-unit').value = current.unit;
                document.getElementById('edit-min').value = current.min_stock;
                renderEditWarehouseStocks(current);
                document.getElementById('edit-condition').value = current.condition;
                document.getElementById('edit-availability').value = current.availability;
                document.getElementById('edit-desc').value = current.description;
                document.getElementById('edit-serial-example').value = current.serial_example || '';

                editModal.classList.remove('hidden');
            });

            const closeEditModal = () => editModal.classList.add('hidden');
            document.getElementById('btn-close-edit-modal').addEventListener('click', closeEditModal);
            document.getElementById('btn-cancel-edit-modal').addEventListener('click', closeEditModal);

            document.getElementById('form-edit-product').addEventListener('submit', async (e) => {
                e.preventDefault();
                const code = document.getElementById('edit-id-hidden').value;
                const current = productMap[code];
                if (!current) return;

                const form = e.currentTarget;
                const submitButton = form.querySelector('[type="submit"]');
                submitButton.disabled = true;

                try {
                    const data = await sendInventoryRequest(
                        inventoryUpdateUrl.replace('__ID__', encodeURIComponent(code)),
                        'PUT',
                        {
                            sku: code,
                            name: document.getElementById('edit-name').value.trim(),
                            category: document.getElementById('edit-category').value,
                            unit: document.getElementById('edit-unit').value.trim(),
                            min_stock: parseInt(document.getElementById('edit-min').value) || 0,
                            stocks: Array.from(editWarehouseStocks.querySelectorAll('.edit-warehouse-stock')).map(input => ({
                                warehouse_id: Number(input.dataset.warehouseId),
                                quantity: Number.parseInt(input.value, 10) || 0,
                            })),
                            condition: document.getElementById('edit-condition').value,
                            availability: document.getElementById('edit-availability').value,
                            description: document.getElementById('edit-desc').value.trim(),
                            serial_example: document.getElementById('edit-serial-example').value.trim() || null,
                        }
                    );

                    showToast(data.message || 'Peralatan berhasil diperbarui.');
                    setTimeout(() => window.location.reload(), 500);
                } catch (error) {
                    showToast(error.message || 'Gagal memperbarui peralatan.');
                    submitButton.disabled = false;
                }
            });

            // 6. Hapus Produk
            document.getElementById('btn-delete-product').addEventListener('click', async () => {
                const current = productMap[currentSelectedId];
                if (!current) return;

                if (confirm(`Apakah Anda yakin ingin menghapus peralatan "${current.name}" (${current.id})?`)) {
                    try {
                        const data = await sendInventoryRequest(
                            inventoryDestroyUrl.replace('__ID__', encodeURIComponent(current.id)),
                            'DELETE'
                        );
                        showToast(data.message || 'Peralatan berhasil dihapus.');
                        setTimeout(() => window.location.reload(), 500);
                    } catch (error) {
                        showToast(error.message || 'Gagal menghapus peralatan.');
                    }
                }
            });

            // Inisialisasi awal produk pertama
            if (productList.length) populateDetail(productList[0].id);

            // Refresh icons
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</x-app-layout>
