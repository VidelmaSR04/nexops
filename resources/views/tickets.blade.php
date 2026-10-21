<x-app-layout>

    {{-- ============================================ --}}
    @php
        $ticketsById = collect($rawTickets)->keyBy('db_id')->all();
    @endphp

    <div class="space-y-6">

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700" role="status">
                {{ session('success') }}
            </div>
        @endif

        {{-- ============================================ --}}
        {{-- 1. HEADER HALAMAN TICKET                     --}}
        {{-- ============================================ --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-dark tracking-tight">Ticketing</h1>
                <p class="text-secondary text-sm mt-0.5">Layanan Pengaduan dan Penanganan Permasalahan TIK OPD</p>
            </div>
            <div>
                <button type="button" id="btn-create-ticket" onclick="openTicketModal('create')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-white text-sm font-medium hover:bg-blue-700 shadow-sm transition-colors">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Buat Tiket</span>
                </button>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- 2. SUMMARY CARDS                             --}}
        {{-- ============================================ --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Tiket Masuk --}}
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i data-lucide="ticket" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Tiket Masuk</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">{{ $summary['open'] }}</p>
                </div>
            </div>

            {{-- In Progress --}}
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center shrink-0">
                    <i data-lucide="loader-2" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Sedang Ditangani</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">{{ $summary['in_progress'] }}</p>
                </div>
            </div>

            {{-- Overdue --}}
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Melebihi Target Waktu</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">{{ $summary['overdue'] }}</p>
                </div>
            </div>

            {{-- Resolved --}}
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Tiket Selesai</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">{{ $summary['resolved'] }}</p>
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
                                   placeholder="Cari no. tiket, OPD, pelapor, atau keluhan..."
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
                            <option value="Assigned">Ditugaskan</option>
                            <option value="In Progress">Sedang Ditangani</option>
                            <option value="Waiting">Menunggu</option>
                            <option value="Resolved">Selesai</option>
                            <option value="Closed">Ditutup</option>
                        </select>

                        <select id="filter-priority" class="text-xs rounded-lg border border-border bg-white px-2.5 py-1.5 text-dark">
                            <option value="">Semua Prioritas</option>
                            <option value="Low">Rendah</option>
                            <option value="Normal">Normal</option>
                            <option value="High">Tinggi</option>
                            <option value="Critical">Kritis</option>
                        </select>

                        <select id="filter-tech" class="text-xs rounded-lg border border-border bg-white px-2.5 py-1.5 text-dark">
                            <option value="">Semua Petugas</option>
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
                                <th class="py-3 pr-3">No. Tiket</th>
                                <th class="py-3 pr-3">OPD Pelapor</th>
                                <th class="py-3 pr-3">Nama Pelapor</th>
                                <th class="py-3 pr-3">Kontak Pelapor</th>
                                <th class="py-3 pr-3">Keluhan / Permasalahan</th>
                                <th class="py-3 pr-3">Prioritas</th>
                                <th class="py-3 pr-3">Status</th>
                                <th class="py-3 pr-3">Petugas Teknis</th>
                                <th class="py-3 pr-3 whitespace-nowrap">Tanggal Laporan</th>
                                <th class="py-3 pr-4 text-center w-8">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="ticket-table-body" class="divide-y divide-gray-100">
                            @foreach ($rawTickets as $idx => $t)
                                <tr class="ticket-row cursor-pointer transition-colors hover:bg-blue-50/40 {{ $idx === 0 ? 'bg-blue-50/70 border-l-4 border-l-primary' : '' }}"
                                    data-id="{{ $t['db_id'] }}"
                                    data-status="{{ $t['status'] }}"
                                    data-priority="{{ $t['priority'] }}"
                                    data-tech="{{ $t['tech'] }}"
                                    data-search="{{ strtolower($t['id'] . ' ' . $t['customer'] . ' ' . ($t['reporter_name'] ?? '') . ' ' . ($t['reporter_contact'] ?? '') . ' ' . $t['title'] . ' ' . $t['subtitle']) }}">
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
                                        @if (($t['reporter_name'] ?? '-') !== '-')
                                            <p class="font-medium text-dark">{{ $t['reporter_name'] }}</p>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 pr-3 whitespace-nowrap">
                                        @if (($t['reporter_contact'] ?? '-') !== '-' && $t['reporter_contact'] !== '')
                                            <span class="text-dark">{{ $t['reporter_contact'] }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
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
                                    <td class="py-3 pr-4 text-center relative">
                                        <button type="button" class="p-1 rounded-md text-gray-400 hover:text-dark hover:bg-gray-100" onclick="toggleMenu({{ $t['db_id'] }})">
                                            <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                        </button>
                                        <div id="menu-{{ $t['db_id'] }}" class="absolute right-0 mt-2 w-40 bg-white rounded-xl shadow-lg border border-border z-50 hidden text-left overflow-hidden">
                                            <div class="py-1">
                                                <a href="{{ route('tickets.show', $t['db_id']) }}"
                                                class="block w-full px-4 py-2 text-left text-sm text-dark hover:bg-gray-100">Lihat</a>

                                                <button type="button" onclick="openTicketModal('edit', {{ $t['db_id'] }})"
                                                        class="block w-full px-4 py-2 text-left text-sm text-dark hover:bg-gray-100">Edit</button>

                                                    <form action="{{ route('tickets.destroy', $t['db_id']) }}" method="POST" class="block w-full"
                                                        onsubmit="return confirmDelete(event, this, '{{ $t['id'] }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50">Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            <tr id="empty-state-row" class="hidden">
                                <td colspan="11" class="py-12 text-center text-gray-400">
                                    <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                                    Tidak ada tiket yang cocok dengan pencarian / filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="p-3.5 border-t border-border flex flex-wrap items-center justify-between gap-3 text-xs">
                    <p id="table-info" class="text-secondary">Menampilkan {{ count($rawTickets) }} dari {{ count($rawTickets) }} tiket</p>
                </div>

            </div>

            {{-- ======================================== --}}
            {{-- KOLOM KANAN: PANEL DETAIL TICKET         --}}
            {{-- ======================================== --}}
            <aside id="ticket-detail-panel"
                   class="w-full xl:w-[480px] 2xl:w-[520px] bg-white rounded-2xl border border-border shadow-[0_2px_6px_rgba(0,0,0,0.02)] p-5 shrink-0 transition-all duration-300 xl:sticky xl:top-20 {{ empty($rawTickets) ? 'hidden' : '' }}">

                {{-- Header Detail Panel --}}
                <div class="flex items-start justify-between pb-4 border-b border-border">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span id="detail-ticket-id" class="text-lg font-bold text-primary">-</span>
                            <span id="detail-priority-badge"></span>
                            <span id="detail-status-badge"></span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-secondary">
                            <span id="detail-created-at">-</span>
                            <span>•</span>
                            <span class="font-medium text-amber-600 flex items-center gap-1">
                                <i data-lucide="clock" class="w-3 h-3"></i> SLA: <span id="detail-sla-left">-</span>
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
                    <h2 id="detail-title" class="text-base font-bold text-dark">-</h2>
                    <p id="detail-subtitle" class="text-xs text-secondary mt-1">-</p>
                </div>

                {{-- Informasi Ringkas 4 Kolom Horizontal --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 p-3 rounded-xl bg-surface border border-border/80 text-xs mb-4">
                    <div>
                        <p class="text-[10px] text-secondary uppercase font-semibold">OPD Pelapor</p>
                        <p id="detail-card-customer" class="font-bold text-dark mt-0.5 truncate">-</p>
                        <p id="detail-card-location" class="text-[10px] text-secondary">-</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-secondary uppercase font-semibold">Petugas Kominfo</p>
                        <p id="detail-card-tech" class="font-bold text-dark mt-0.5 truncate">-</p>
                        <p id="detail-tech-state" class="text-[10px] text-emerald-600 font-medium">-</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-secondary uppercase font-semibold">Aset TIK</p>
                        <p id="detail-card-asset" class="font-bold text-dark mt-0.5 truncate">-</p>
                        <p id="detail-card-serial" class="text-[10px] text-secondary truncate">SN: -</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-secondary uppercase font-semibold">Prioritas</p>
                        <p id="detail-card-priority" class="font-bold text-red-600 mt-0.5">-</p>
                        <p class="text-[10px] text-secondary">Prioritas</p>
                    </div>
                </div>

                {{-- 4 Tab Navigation --}}
                <div class="flex border-b border-border text-xs font-semibold mb-4 gap-6" id="panel-tabs">
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-primary text-primary transition-colors" data-target="tab-detail">Detail</button>
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-transparent text-secondary hover:text-dark transition-colors" data-target="tab-material">Material & Perangkat</button>
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-transparent text-secondary hover:text-dark transition-colors" data-target="tab-activity">Aktivitas</button>
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-transparent text-secondary hover:text-dark transition-colors" data-target="tab-attachment">Lampiran</button>
                </div>

                {{-- Konten Tab Panel --}}
                <div class="space-y-4">

                    {{-- TAB 1: DETAIL --}}
                    <div id="tab-detail" class="tab-pane space-y-3.5">
                        <div class="space-y-2.5 text-xs">
                            <div>
                                <span class="text-secondary text-[11px] block">Nomor Tiket:</span>
                                <span id="info-ticket-id" class="font-semibold text-dark">-</span>
                            </div>
                            <div>
                                <span class="text-secondary text-[11px] block">Judul:</span>
                                <span id="info-title" class="font-medium text-dark">-</span>
                            </div>
                            <div>
                                <span class="text-secondary text-[11px] block">Deskripsi:</span>
                                <p id="info-description" class="text-dark bg-surface p-2.5 rounded-xl border border-border mt-1 leading-relaxed">
                                    -
                                </p>
                            </div>
                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div>
                                    <span class="text-secondary text-[11px] block">OPD Pelapor:</span>
                                    <span id="info-customer" class="font-medium text-dark">-</span>
                                </div>
                                <div>
                                    <span class="text-secondary text-[11px] block">Nama Pelapor:</span>
                                    <span id="info-reporter" class="font-medium text-dark">-</span>
                                </div>
                                <div>
                                    <span class="text-secondary text-[11px] block">Petugas Teknis:</span>
                                    <span id="info-tech" class="font-medium text-dark">-</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div>
                                    <span class="text-secondary text-[11px] block">Kontak Pelapor:</span>
                                    <span id="info-reporter-contact" class="font-medium text-dark">-</span>
                                </div>
                                <div>
                                    <span class="text-secondary text-[11px] block">Target Selesai:</span>
                                    <span id="info-sla-due" class="font-medium text-dark">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 2: MATERIAL --}}
                    <div id="tab-material" class="tab-pane hidden space-y-2.5 text-xs">
                        <p class="font-semibold text-dark text-xs mb-2">Material / Sparepart Digunakan:</p>
                        <ul id="material-list" class="space-y-2">
                            <li class="text-secondary py-3 text-center">Belum ada material digunakan.</li>
                        </ul>
                    </div>

                    {{-- TAB 3: ACTIVITY --}}
                    <div id="tab-activity" class="tab-pane hidden space-y-3 text-xs">
                        <p class="font-semibold text-dark text-xs mb-2">Riwayat Aktivitas Tiket:</p>
                        <ul id="activity-list" class="space-y-3"></ul>
                    </div>

                    {{-- TAB 4: ATTACHMENT --}}
                    <div id="tab-attachment" class="tab-pane hidden space-y-2.5 text-xs">
                        <p class="font-semibold text-dark text-xs mb-2">Lampiran File / Foto:</p>
                        <ul id="attachment-list" class="space-y-2">
                            <li class="text-secondary py-3 text-center">Tidak ada lampiran.</li>
                        </ul>
                    </div>

                    {{-- Status & Aksi Card --}}
                    <div class="p-3.5 rounded-xl border border-border bg-surface space-y-3">
                        <p class="font-semibold text-dark text-xs">Status & Aksi</p>
                        <select id="action-status-select" class="w-full text-xs rounded-lg border border-border bg-white px-3 py-2 text-dark focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <option value="Open">Open</option>
                            <option value="Assigned">Ditugaskan</option>
                            <option value="In Progress">Sedang Ditangani</option>
                            <option value="Waiting">Menunggu</option>
                            <option value="Resolved">Selesai</option>
                            <option value="Closed">Ditutup</option>
                        </select>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" id="btn-assign-tech" class="col-span-2 py-2 px-3 rounded-lg bg-primary text-white text-xs font-semibold hover:bg-blue-700 shadow-sm transition-colors text-center">
                                Tugaskan Petugas
                            </button>
                            <button type="button" id="btn-update-status" class="py-2 px-3 rounded-lg bg-white border border-border text-dark text-xs font-medium hover:bg-gray-50 transition-colors text-center">
                                Ubah Status
                            </button>
                            <button type="button" id="btn-edit-ticket" class="py-2 px-3 rounded-lg bg-white border border-border text-dark text-xs font-medium hover:bg-gray-50 transition-colors text-center">
                                Edit Tiket
                            </button>
                            <button type="button" id="btn-close-ticket" class="col-span-2 py-2 px-3 rounded-lg bg-white border border-red-200 text-danger text-xs font-medium hover:bg-red-50 transition-colors text-center">
                                Tutup Tiket
                            </button>
                        </div>
                    </div>

                    {{-- Quick Info Card --}}
                    <div class="p-3.5 rounded-xl border border-border bg-surface space-y-2.5 text-xs">
                        <p class="font-semibold text-dark text-xs">Quick Info</p>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-secondary text-[11px]">OPD Pelapor:</p>
                                <p id="quick-customer" class="font-medium text-dark">-</p>
                            </div>
                            <a href="{{ route('customers.index') }}" class="text-primary text-[11px] font-semibold hover:underline">Lihat Detail</a>
                        </div>
                        <div class="flex items-center justify-between border-t border-gray-200/60 pt-2">
                            <div>
                                <p class="text-secondary text-[11px]">Aset TIK Terkait:</p>
                                <p id="quick-asset" class="font-medium text-dark">-</p>
                            </div>
                            <a href="{{ route('assets.index') }}" class="text-primary text-[11px] font-semibold hover:underline">Lihat Detail</a>
                        </div>
                    </div>

                </div>

            </aside>

        </div>

    </div>

   {{-- MODAL: BUAT / EDIT TICKET --}}
@php
    $modalInput = 'w-full rounded-xl border border-border bg-white px-3 py-2 text-xs text-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary';
@endphp
<div id="ticket-modal" class="fixed inset-0 z-[60] hidden items-center justify-center p-4 bg-black/40" role="dialog" aria-modal="true" aria-labelledby="ticket-modal-title">
    <div class="bg-white rounded-2xl border border-border shadow-2xl w-full max-w-lg max-h-[92vh] overflow-y-auto">
        <div class="p-4 border-b border-border flex items-start justify-between gap-3 sticky top-0 bg-white rounded-t-2xl">
            <div>
                <h3 id="ticket-modal-title" class="text-base font-bold text-dark">Buat Tiket</h3>
                <p id="ticket-modal-sub" class="text-secondary text-xs mt-0.5">Isi informasi pengaduan dari OPD</p>
            </div>
            <button type="button" data-close-ticket-modal class="p-1.5 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100" aria-label="Tutup">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="ticket-form" method="POST" action="{{ route('tickets.store') }}" class="p-5 space-y-3.5 text-xs" novalidate>
            @csrf
            <input type="hidden" name="_method" id="ticket-form-method" value="PUT" disabled>

            <div id="ticket-form-errors" class="hidden rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-red-700 space-y-0.5"></div>

            <div>
                <label for="tf-customer" class="block font-medium text-dark mb-1">OPD Pelapor</label>
                <select id="tf-customer" name="customer_id" required class="{{ $modalInput }}">
                    <option value="">Pilih OPD</option>
                    @foreach ($opds as $opd)
                        <option value="{{ $opd->id }}" data-reporter="{{ $opd->reporter_name }}" data-contact="{{ $opd->reporter_contact }}">{{ $opd->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="tf-reporter" class="block font-medium text-dark mb-1">Nama Pelapor</label>
                    <input id="tf-reporter" type="text" name="reporter_name" required maxlength="150" placeholder="Nama staf OPD" class="{{ $modalInput }}">
                </div>
                <div>
                    <label for="tf-contact" class="block font-medium text-dark mb-1">Kontak Pelapor</label>
                    <input id="tf-contact" type="text" name="reporter_contact" maxlength="50" placeholder="No. HP / WhatsApp" class="{{ $modalInput }}">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="tf-location" class="block font-medium text-dark mb-1">Lokasi</label>
                    <input id="tf-location" type="text" name="location" required maxlength="100" class="{{ $modalInput }}">
                </div>
                <div>
                    <label for="tf-priority" class="block font-medium text-dark mb-1">Prioritas</label>
                    <select id="tf-priority" name="priority" required class="{{ $modalInput }}">
                        @foreach (['Low' => 'Rendah', 'Normal' => 'Normal', 'High' => 'Tinggi', 'Critical' => 'Kritis'] as $priority => $priorityLabel)
                            <option value="{{ $priority }}" @selected($priority === 'Normal')>{{ $priorityLabel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="tf-title" class="block font-medium text-dark mb-1">Judul Masalah</label>
                <input id="tf-title" type="text" name="title" required maxlength="255" class="{{ $modalInput }}">
            </div>

            <div>
                <label for="tf-description" class="block font-medium text-dark mb-1">Deskripsi</label>
                <textarea id="tf-description" name="description" rows="3" class="{{ $modalInput }}"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="tf-tech" class="block font-medium text-dark mb-1">Teknisi</label>
                    <select id="tf-tech" name="assigned_tech_name" class="{{ $modalInput }}">
                        <option value="-">Belum ditugaskan</option>
                        @foreach ($technicians as $tn)
                            <option value="{{ $tn }}">{{ $tn }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tf-asset" class="block font-medium text-dark mb-1">Perangkat / Aset</label>
                    <input id="tf-asset" type="text" name="asset_name" class="{{ $modalInput }}">
                </div>
            </div>

            <div class="pt-3 border-t border-border flex items-center justify-end gap-2">
                <button type="button" data-close-ticket-modal class="px-3.5 py-2 rounded-xl border border-border text-secondary hover:bg-gray-50 font-medium">Batal</button>
                <button id="ticket-form-submit" type="submit" class="px-4 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-blue-700 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">Simpan Ticket</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: KONFIRMASI (hapus / persetujuan update) --}}
<div id="confirm-modal" class="fixed inset-0 z-[80] hidden items-center justify-center p-4 bg-black/40" role="alertdialog" aria-modal="true" aria-labelledby="confirm-modal-title" aria-describedby="confirm-modal-message">
    <div class="bg-white rounded-2xl border border-border shadow-2xl w-full max-w-sm">
        <div class="p-5 text-center space-y-3 text-xs">
            <div id="confirm-icon-danger" class="mx-auto w-12 h-12 rounded-full bg-red-50 text-red-600 flex items-center justify-center">
                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>
            <div id="confirm-icon-primary" class="hidden mx-auto w-12 h-12 rounded-full bg-blue-50 text-primary flex items-center justify-center">
                <i data-lucide="help-circle" class="w-6 h-6"></i>
            </div>
            <h3 id="confirm-modal-title" class="text-base font-bold text-dark"></h3>
            <p id="confirm-modal-message" class="text-secondary leading-relaxed"></p>
        </div>
        <div class="p-4 border-t border-border flex items-center justify-end gap-2 text-xs">
            <button type="button" id="confirm-modal-cancel" class="px-3.5 py-2 rounded-xl border border-border text-secondary hover:bg-gray-50 font-medium">Batal</button>
            <button type="button" id="confirm-modal-ok" class="px-4 py-2 rounded-xl text-white font-semibold shadow-sm"></button>
        </div>
    </div>
</div>

{{-- Toast Notifikasi --}}
<div id="toast-notification"
     class="fixed bottom-5 right-5 z-[70] hidden max-w-sm rounded-xl bg-dark text-white text-xs px-4 py-3 shadow-xl transition-all">
</div>

    {{-- ============================================ --}}
    {{-- 5. JAVASCRIPT LOGIC INTERAKTIF              --}}
    {{-- ============================================ --}}
        <script>
        // ===== POP-UP KONFIRMASI =====
        let confirmResolver = null;
        const confirmModal = document.getElementById('confirm-modal');
        const confirmOk = document.getElementById('confirm-modal-ok');
        const confirmCancel = document.getElementById('confirm-modal-cancel');

        function closeConfirm(result) {
            confirmModal.classList.add('hidden');
            confirmModal.classList.remove('flex');
            if (confirmResolver) {
                const resolve = confirmResolver;
                confirmResolver = null;
                resolve(result);
            }
        }

        function askConfirm({ title, message, confirmText = 'Ya, lanjutkan', cancelText = 'Batal', variant = 'primary' }) {
            if (confirmResolver) closeConfirm(false);

            return new Promise(resolve => {
                confirmResolver = resolve;
                const danger = variant === 'danger';

                document.getElementById('confirm-modal-title').textContent = title;
                document.getElementById('confirm-modal-message').textContent = message;
                document.getElementById('confirm-icon-danger').classList.toggle('hidden', !danger);
                document.getElementById('confirm-icon-primary').classList.toggle('hidden', danger);

                confirmOk.textContent = confirmText;
                confirmCancel.textContent = cancelText;
                confirmOk.className = 'px-4 py-2 rounded-xl text-white font-semibold shadow-sm '
                    + (danger ? 'bg-red-600 hover:bg-red-700' : 'bg-primary hover:bg-blue-700');

                confirmModal.classList.remove('hidden');
                confirmModal.classList.add('flex');
                (danger ? confirmCancel : confirmOk).focus(); // aksi berbahaya: fokus default ke Batal
            });
        }

        function confirmDelete(event, form, label) {
            event.preventDefault();
            document.querySelectorAll('[id^="menu-"]').forEach(el => el.classList.add('hidden'));

            askConfirm({
                title: 'Hapus ticket?',
                message: `Tiket ${label} akan dihapus dari daftar.`,
                confirmText: 'Ya, hapus',
                variant: 'danger',
            }).then(ok => { if (ok) form.submit(); }); // form.submit() tidak memicu onsubmit lagi

            return false;
        }

        confirmOk.addEventListener('click', () => closeConfirm(true));
        confirmCancel.addEventListener('click', () => closeConfirm(false));
        confirmModal.addEventListener('click', (e) => { if (e.target === confirmModal) closeConfirm(false); });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !confirmModal.classList.contains('hidden')) {
                e.stopImmediatePropagation(); // Esc hanya menutup konfirmasi, bukan modal ticket di bawahnya
                closeConfirm(false);
            }
        }, true);

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

            function escapeHtml(value) {
                const element = document.createElement('span');
                element.textContent = value ?? '';
                return element.innerHTML;
            }

            let selectedTicketId = null;

            async function updateTicketStatus(status) {
                if (!selectedTicketId) return;

                const ok = await askConfirm({
                    title: status === 'Closed' ? 'Tutup ticket?' : 'Ubah status ticket?',
                    message: status === 'Closed'
                        ? 'Ticket akan ditutup dan penugasan teknisi dinonaktifkan.'
                        : `Status ticket akan diubah menjadi "${status}".`,
                    confirmText: status === 'Closed' ? 'Ya, tutup' : 'Ya, ubah',
                    variant: status === 'Closed' ? 'danger' : 'primary',
                });
                if (!ok) return;

                const button = document.getElementById('btn-update-status');
                button.disabled = true;

                try {
                    const response = await fetch(`/tickets/${selectedTicketId}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ status }),
                    });
                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Gagal mengubah status tiket.');
                    }

                    showToast(data.message);
                    setTimeout(() => window.location.reload(), 700);
                } catch (error) {
                    showToast(`❌ ${error.message}`);
                    button.disabled = false;
                }
            }

            document.getElementById('btn-update-status').addEventListener('click', () => {
                updateTicketStatus(document.getElementById('action-status-select').value);
            });
            document.getElementById('btn-close-ticket').addEventListener('click', () => {
                updateTicketStatus('Closed');
            });
            document.getElementById('btn-edit-ticket').addEventListener('click', () => {
                if (selectedTicketId) openTicketModal('edit', selectedTicketId);
            });
            document.getElementById('btn-assign-tech').addEventListener('click', () => {
                if (selectedTicketId) openTicketModal('edit', selectedTicketId, 'tf-tech');
            });

            // ===== MODAL BUAT / EDIT TICKET =====
            const ticketModal = document.getElementById('ticket-modal');
            const ticketForm = document.getElementById('ticket-form');
            const ticketFormErrors = document.getElementById('ticket-form-errors');
            const ticketMethodInput = document.getElementById('ticket-form-method');
            const ticketSubmitBtn = document.getElementById('ticket-form-submit');
            const ticketStoreUrl = @json(route('tickets.store'));
            const ticketUpdateUrl = @json(route('tickets.update', ['ticket' => '__ID__']));
            const opdContacts = @json($opds->keyBy('id')->map(fn ($opd) => ['reporter_name' => $opd->reporter_name, 'reporter_contact' => $opd->reporter_contact]));

            function clearTicketErrors() {
                ticketFormErrors.classList.add('hidden');
                ticketFormErrors.innerHTML = '';
            }

            function showTicketErrors(messages) {
                ticketFormErrors.innerHTML = messages.map(m => `<p>• ${escapeHtml(m)}</p>`).join('');
                ticketFormErrors.classList.remove('hidden');
            }

            function closeTicketModal() {
                ticketModal.classList.add('hidden');
                ticketModal.classList.remove('flex');
                document.body.classList.remove('overflow-y-hidden');
            }

            function openTicketModal(mode, id = null, focusId = 'tf-customer') {
                clearTicketErrors();
                ticketForm.reset();
                ticketSubmitBtn.disabled = false;

                const title = document.getElementById('ticket-modal-title');
                const sub = document.getElementById('ticket-modal-sub');

                if (mode === 'edit') {
                    const t = rawData[id];
                    if (!t) return;

                    title.textContent = `Edit Ticket ${t.id}`;
                    sub.textContent = 'Perbarui data ticket atau tugaskan teknisi';
                    ticketSubmitBtn.textContent = 'Simpan Perubahan';
                    ticketForm.action = ticketUpdateUrl.replace('__ID__', t.db_id);
                    ticketMethodInput.disabled = false;

                    const blank = v => (v === '-' || v == null) ? '' : v;
                    const opdOption = Array.from(document.getElementById('tf-customer').options)
                        .find(option => option.textContent === t.customer);
                    document.getElementById('tf-customer').value = t.customer_id || opdOption?.value || '';
                    document.getElementById('tf-location').value = blank(t.location);
                    document.getElementById('tf-reporter').value = blank(t.reporter_name);
                    document.getElementById('tf-contact').value = blank(t.reporter_contact);
                    document.getElementById('tf-priority').value = t.priority;
                    document.getElementById('tf-title').value = blank(t.title);
                    document.getElementById('tf-description').value = blank(t.description);
                    document.getElementById('tf-tech').value = t.tech || '-';
                    document.getElementById('tf-asset').value = blank(t.asset);
                } else {
                    title.textContent = 'Buat Ticket';
                    sub.textContent = 'Isi informasi pengaduan dari OPD';
                    ticketSubmitBtn.textContent = 'Simpan Ticket';
                    ticketForm.action = ticketStoreUrl;
                    ticketMethodInput.disabled = true;
                }

                // Tutup menu titik tiga yang mungkin masih terbuka
                document.querySelectorAll('[id^="menu-"]').forEach(el => el.classList.add('hidden'));

                ticketModal.classList.remove('hidden');
                ticketModal.classList.add('flex');
                document.body.classList.add('overflow-y-hidden');
                setTimeout(() => document.getElementById(focusId)?.focus(), 50);
            }
            window.openTicketModal = openTicketModal;

            document.getElementById('tf-customer').addEventListener('change', (event) => {
                const contact = opdContacts[event.target.value];
                if (contact) {
                    document.getElementById('tf-reporter').value = contact.reporter_name || '';
                    document.getElementById('tf-contact').value = contact.reporter_contact || '';
                }
            });

            ticketForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                clearTicketErrors();

                // Minta persetujuan hanya saat mode edit
                if (!ticketMethodInput.disabled) {
                    const ok = await askConfirm({
                        title: 'Simpan perubahan?',
                        message: 'Data ticket ini akan diperbarui.',
                        confirmText: 'Ya, simpan',
                    });
                    if (!ok) return;
                }

                ticketSubmitBtn.disabled = true;

                try {
                    const response = await fetch(ticketForm.action, {
                        method: 'POST', // _method=PUT ikut terkirim lewat FormData saat edit
                        body: new FormData(ticketForm),
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                    });

                    if (response.status === 422) {
                        const data = await response.json();
                        showTicketErrors(Object.values(data.errors || {}).flat());
                        ticketSubmitBtn.disabled = false;
                        return;
                    }

                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw new Error(data.message || 'Gagal menyimpan ticket.');
                    }

                    closeTicketModal();
                    showToast(`✅ ${data.message || 'Ticket berhasil disimpan.'}`);
                    setTimeout(() => window.location.reload(), 700);
                } catch (error) {
                    showTicketErrors([error.message]);
                    ticketSubmitBtn.disabled = false;
                }
            });

            document.querySelectorAll('[data-close-ticket-modal]').forEach(btn => {
                btn.addEventListener('click', closeTicketModal);
            });
            ticketModal.addEventListener('click', (e) => {
                if (e.target === ticketModal) closeTicketModal();
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !ticketModal.classList.contains('hidden')) closeTicketModal();
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
                    const idA = Number(a.dataset.id);
                    const idB = Number(b.dataset.id);
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
                tableInfo.textContent = `Menampilkan ${visibleCount} dari ${rows.length} tiket`;
            }

            [searchInput, statusFilter, priorityFilter, techFilter, sortSelect].forEach(el => {
                el.addEventListener(el === searchInput ? 'input' : 'change', applyFilters);
            });
            applyFilters();

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

                selectedTicketId = t.db_id;
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
                document.getElementById('info-reporter').textContent = t.reporter_name || '-';
                document.getElementById('info-reporter-contact').textContent = (t.reporter_contact && t.reporter_contact !== '-') ? t.reporter_contact : '-';
                document.getElementById('quick-customer').textContent = t.customer;
                document.getElementById('detail-card-location').textContent = t.location;

                document.getElementById('detail-card-tech').textContent = t.tech;
                document.getElementById('info-tech').textContent = t.tech;
                document.getElementById('detail-tech-state').textContent = t.tech === '-' ? 'Belum ditugaskan' : 'Ditugaskan';
                const creatorEl = document.getElementById('info-creator'); if (creatorEl) creatorEl.textContent = t.creator;

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
                document.getElementById('btn-close-ticket').disabled = t.status === 'Closed';

                const activityList = document.getElementById('activity-list');
                activityList.innerHTML = t.activities && t.activities.length
                    ? t.activities.map(activity => `
                        <li class="border-l-2 border-primary py-1 pl-3">
                            <p class="font-medium text-dark">${escapeHtml(activity.description)}</p>
                            <p class="mt-1 text-[11px] text-secondary">${escapeHtml(activity.user)} · ${escapeHtml(activity.created_at)}</p>
                        </li>`).join('')
                    : '<li class="text-secondary">Belum ada riwayat aktivitas.</li>';

                // Materials
                const matList = document.getElementById('material-list');
                matList.innerHTML = t.materials && t.materials.length
                    ? t.materials.map(m => `
                        <li class="flex items-center justify-between p-2.5 rounded-xl border border-border bg-surface">
                            <span class="font-medium text-dark">${escapeHtml(m.name)}</span>
                            <span class="text-secondary font-semibold">${escapeHtml(m.qty)} ${escapeHtml(m.unit)}</span>
                        </li>`).join('')
                    : '<li class="text-secondary py-3 text-center">Belum ada material digunakan.</li>';

                // Attachments
                const attList = document.getElementById('attachment-list');
                attList.innerHTML = t.attachments && t.attachments.length
                    ? t.attachments.map(a => `
                        <li class="flex items-center justify-between p-2.5 rounded-xl border border-border bg-surface">
                            <div class="flex items-center gap-2">
                                <i data-lucide="paperclip" class="w-4 h-4 text-secondary"></i>
                                <span class="font-medium text-dark">${escapeHtml(a.name)}</span>
                            </div>
                            <span class="text-[11px] text-secondary">${escapeHtml(a.size)}</span>
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

            if (rows.length > 0) {
                rows[0].classList.add('bg-blue-50/70', 'border-l-4', 'border-l-primary');
                populateDetail(rows[0].dataset.id);
            }

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

            // Refresh Lucide Icons
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
    <script>
        function toggleMenu(id) {
            document.querySelectorAll('[id^="menu-"]').forEach(el => {
                if (el.id !== `menu-${id}`) el.classList.add('hidden');
            });
            const menu = document.getElementById('menu-' + id);
            if (menu) menu.classList.toggle('hidden');
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('[id^="menu-"]') && !e.target.closest('button[onclick^="toggleMenu"]')) {
                document.querySelectorAll('[id^="menu-"]').forEach(el => el.classList.add('hidden'));
            }
        });
    </script>
</x-app-layout>
