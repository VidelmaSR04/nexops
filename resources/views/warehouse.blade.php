<x-app-layout>

    @php
        // Data dari WarehouseController@index (kunci: id, name, location, status).
        // Kunci opsional yang belum dikirim controller: assets_count (jumlah aset) dan
        // repairs (riwayat perbaikan: ticket, date, complaint, technician, status).
        $warehouses = $warehouses ?? [];
        $statuses   = ['Aktif', 'Maintenance', 'Nonaktif'];
        $opdNames   = collect($warehouses)->pluck('name')->filter()->unique()->sort()->values()->all();

        $stat = [
            'total'    => count($warehouses),
            'opd'      => count($opdNames),
            'with'     => collect($warehouses)->filter(fn ($w) => (int) ($w['assets_count'] ?? 0) > 0)->count(),
            'problem'  => collect($warehouses)->whereIn('status', ['Maintenance', 'Nonaktif'])->count(),
        ];
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

        {{-- 1. HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-dark tracking-tight">Lokasi OPD</h1>
                <p class="text-secondary text-sm mt-0.5">Kelola lokasi kantor dan penempatan perangkat TIK di lingkungan OPD Kabupaten Situbondo.</p>
            </div>
            <div>
                <button type="button" id="btn-open-create-modal"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-white text-sm font-medium hover:bg-blue-700 shadow-sm transition-colors">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Tambah Lokasi</span>
                </button>
            </div>
        </div>

        {{-- 2. KARTU STATISTIK --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ([
                ['Total Lokasi', $stat['total'], 'map-pin', 'bg-blue-50 text-blue-600'],
                ['OPD Terdaftar', $stat['opd'], 'building-2', 'bg-indigo-50 text-indigo-600'],
                ['Lokasi dengan Aset', $stat['with'], 'package-check', 'bg-emerald-50 text-emerald-600'],
                ['Lokasi Bermasalah', $stat['problem'], 'alert-triangle', 'bg-red-50 text-red-600'],
            ] as [$label, $value, $icon, $color])
                <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                    <div class="w-11 h-11 rounded-xl {{ $color }} flex items-center justify-center shrink-0">
                        <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-xs text-secondary font-medium">{{ $label }}</p>
                        <p class="text-2xl font-bold text-dark mt-0.5">{{ number_format($value) }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- 3. TABEL + PANEL DETAIL --}}
        <div class="flex flex-col xl:flex-row items-start gap-5">

            {{-- KIRI: TABEL --}}
            <div id="table-container" class="w-full xl:flex-1 min-w-0 bg-white rounded-2xl border border-border shadow-[0_2px_6px_rgba(0,0,0,0.02)] overflow-hidden">

                <div class="p-4 border-b border-border space-y-3">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="relative flex-1 min-w-[220px]">
                            <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                            <input id="warehouse-search" type="text" placeholder="Cari kode lokasi, nama OPD, atau alamat..."
                                   class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-border bg-surface focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        </div>
                        <button type="button" id="btn-toggle-filters"
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-border text-xs font-medium text-dark hover:bg-gray-50 transition-colors">
                            <i data-lucide="filter" class="w-3.5 h-3.5 text-secondary"></i>
                            <span>Filter</span>
                            <i data-lucide="chevron-down" class="w-3 h-3 text-gray-400"></i>
                        </button>
                        <select id="warehouse-sort" class="text-xs rounded-xl border border-border bg-white px-3 py-2 text-dark focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <option value="code_asc">Kode Lokasi (A-Z)</option>
                            <option value="name_asc">Nama OPD (A-Z)</option>
                            <option value="assets_desc">Jumlah Aset Terbanyak</option>
                        </select>
                    </div>

                    <div id="filter-dropdown-panel" class="hidden grid-cols-1 sm:grid-cols-3 gap-2.5 pt-2 border-t border-gray-100">
                        <select id="filter-opd" class="text-xs rounded-lg border border-border bg-white px-2.5 py-1.5 text-dark">
                            <option value="">Semua OPD</option>
                            @foreach ($opdNames as $n)
                                <option value="{{ $n }}">{{ $n }}</option>
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

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr class="text-secondary bg-[#FAFCFF] border-b border-border font-medium text-[11px] uppercase tracking-wider">
                                <th class="pl-4 py-3 w-8">
                                    <input id="check-all" type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary/20">
                                </th>
                                <th class="py-3 pr-3 whitespace-nowrap">Kode Lokasi</th>
                                <th class="py-3 pr-3 min-w-[180px]">Nama OPD</th>
                                <th class="py-3 pr-3 min-w-[160px]">Alamat</th>
                                <th class="py-3 pr-3 text-right whitespace-nowrap">Jumlah Aset</th>
                                <th class="py-3 pr-3 text-center">Status</th>
                                <th class="py-3 pr-4 text-center w-8">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="warehouse-table-body" class="divide-y divide-gray-100">
                            @foreach ($warehouses as $idx => $wh)
                                @php
                                    $badge = match($wh['status']) {
                                        'Aktif'       => 'bg-emerald-50 text-emerald-600 border border-emerald-200/60',
                                        'Maintenance' => 'bg-amber-50 text-amber-600 border border-amber-200/60 font-semibold',
                                        default       => 'bg-slate-100 text-slate-500 border border-slate-200/60',
                                    };
                                    $assetsCount = $wh['assets_count'] ?? null;
                                @endphp
                                <tr class="warehouse-row cursor-pointer transition-colors hover:bg-blue-50/40 {{ $idx === 0 ? 'bg-blue-50/70 border-l-4 border-l-primary' : '' }}"
                                    data-id="{{ $wh['id'] }}"
                                    data-name="{{ $wh['name'] }}"
                                    data-assets="{{ (int) $assetsCount }}"
                                    data-status="{{ $wh['status'] }}"
                                    data-search="{{ strtolower($wh['id'] . ' ' . $wh['name'] . ' ' . ($wh['location'] ?? '')) }}">
                                    <td class="pl-4 py-3">
                                        <input type="checkbox" class="row-checkbox rounded border-gray-300 text-primary focus:ring-primary/20">
                                    </td>
                                    <td class="py-3 pr-3 font-semibold text-primary whitespace-nowrap">{{ $wh['id'] }}</td>
                                    <td class="py-3 pr-3 font-medium text-dark">{{ $wh['name'] }}</td>
                                    <td class="py-3 pr-3 text-secondary">{{ $wh['location'] ?? '-' }}</td>
                                    <td class="py-3 pr-3 text-right font-bold text-dark">{{ $assetsCount ?? '-' }}</td>
                                    <td class="py-3 pr-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium whitespace-nowrap {{ $badge }}">{{ $wh['status'] }}</span>
                                    </td>
                                    <td class="py-3 pr-4 text-center">
                                        <button type="button" class="p-1 rounded-md text-gray-400 hover:text-dark hover:bg-gray-100" data-warehouse-action="edit" aria-label="Edit {{ $wh['name'] }}">
                                            <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                            <tr id="empty-state-row" class="{{ count($warehouses) ? 'hidden' : '' }}">
                                <td colspan="7" class="py-12 text-center text-gray-400">
                                    <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                                    Tidak ada lokasi yang cocok dengan pencarian / filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="p-3.5 border-t border-border flex flex-wrap items-center justify-between gap-3 text-xs">
                    <p id="table-info" class="text-secondary">Menampilkan {{ count($warehouses) }} dari {{ count($warehouses) }} lokasi</p>
                    <div class="flex items-center gap-1">
                        <button type="button" class="w-7 h-7 rounded-lg border border-border flex items-center justify-center text-secondary hover:bg-gray-50" data-dummy-action="Halaman Sebelumnya">
                            <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
                        </button>
                        <button type="button" class="w-7 h-7 rounded-lg bg-primary text-white font-medium flex items-center justify-center">1</button>
                        <button type="button" class="w-7 h-7 rounded-lg border border-border flex items-center justify-center text-secondary hover:bg-gray-50" data-dummy-action="Halaman Berikutnya">
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- KANAN: PANEL DETAIL (ringkas) --}}
            <aside id="warehouse-detail-panel"
                   class="w-full xl:w-[400px] 2xl:w-[440px] bg-white rounded-2xl border border-border shadow-[0_2px_6px_rgba(0,0,0,0.02)] p-5 shrink-0 xl:sticky xl:top-20">

                <div class="flex items-center gap-2 pb-4 border-b border-border">
                    <span id="detail-code" class="text-lg font-bold text-primary">-</span>
                    <span id="detail-status-badge" class="px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-500 border border-slate-200/60">-</span>
                </div>

                <dl class="grid grid-cols-2 gap-3 text-xs py-4">
                    <div class="col-span-2">
                        <dt class="text-secondary text-[11px]">Nama OPD</dt>
                        <dd id="detail-name" class="font-semibold text-dark mt-0.5">Pilih lokasi</dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-secondary text-[11px]">Alamat</dt>
                        <dd id="detail-address" class="font-medium text-dark mt-0.5">-</dd>
                    </div>
                    <div>
                        <dt class="text-secondary text-[11px]">Kode Lokasi</dt>
                        <dd id="detail-code-2" class="font-medium text-dark mt-0.5">-</dd>
                    </div>
                    <div>
                        <dt class="text-secondary text-[11px]">Jumlah Aset</dt>
                        <dd id="detail-assets" class="font-bold text-dark mt-0.5">-</dd>
                    </div>
                    <div>
                        <dt class="text-secondary text-[11px]">Status</dt>
                        <dd id="detail-status" class="font-medium text-dark mt-0.5">-</dd>
                    </div>
                </dl>

                {{-- Riwayat Perbaikan --}}
                <div class="border-t border-border pt-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-bold text-dark">Riwayat Perbaikan</h3>
                        <a href="#" class="text-primary text-[11px] font-semibold hover:underline" data-dummy-action="Lihat Semua Riwayat (akan terhubung ke modul Tickets)">Lihat Semua Riwayat</a>
                    </div>
                    <div id="repair-list" class="space-y-3 relative before:absolute before:inset-y-1 before:left-[5px] before:w-0.5 before:bg-gray-100"></div>
                    <div id="repair-empty" class="hidden text-center py-6 text-gray-400 text-xs">
                        <i data-lucide="wrench" class="w-6 h-6 mx-auto mb-1.5 text-gray-300"></i>
                        Belum ada riwayat perbaikan
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 pt-4 mt-4 border-t border-border">
                    <button type="button" id="btn-trigger-edit" class="py-2 px-3 rounded-lg bg-primary text-white text-xs font-semibold hover:bg-blue-700 transition-colors">Edit Lokasi</button>
                    <button type="button" id="btn-trigger-delete" class="py-2 px-3 rounded-lg bg-white border border-red-200 text-danger text-xs font-semibold hover:bg-red-50 transition-colors">Hapus Lokasi</button>
                </div>
            </aside>
        </div>
    </div>

    {{-- MODAL TAMBAH --}}
    <div id="modal-create-warehouse" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 hidden">
        <div class="bg-white rounded-2xl border border-border shadow-2xl max-w-lg w-full overflow-hidden">
            <div class="p-4 border-b border-border flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-dark">Tambah Lokasi</h3>
                    <p class="text-secondary text-xs">Daftarkan lokasi kantor OPD</p>
                </div>
                <button type="button" class="btn-close-modal p-1 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form id="form-create-warehouse" action="{{ route('warehouse.store') }}" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1" for="create-code">Kode Lokasi <span class="text-danger">*</span></label>
                        <input type="text" id="create-code" required class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1" for="create-status">Status</label>
                        <select id="create-status" class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                            @foreach ($statuses as $st)<option value="{{ $st }}">{{ $st }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1" for="create-name">Nama OPD <span class="text-danger">*</span></label>
                    <input type="text" id="create-name" list="opd-options" required class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <datalist id="opd-options">@foreach ($opdNames as $n)<option value="{{ $n }}"></option>@endforeach</datalist>
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1" for="create-address">Alamat <span class="text-danger">*</span></label>
                    <input type="text" id="create-address" required class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>
                <div class="pt-3 border-t border-border flex items-center justify-end gap-2">
                    <button type="button" class="btn-close-modal px-3.5 py-2 rounded-xl border border-border text-secondary hover:bg-gray-50 font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-blue-700 shadow-sm">Simpan Lokasi</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div id="modal-edit-warehouse" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 hidden">
        <div class="bg-white rounded-2xl border border-border shadow-2xl max-w-lg w-full overflow-hidden">
            <div class="p-4 border-b border-border flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-dark">Edit Lokasi</h3>
                    <p class="text-secondary text-xs">Ubah data lokasi OPD</p>
                </div>
                <button type="button" class="btn-close-modal p-1 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form id="form-edit-warehouse" class="p-5 space-y-3.5 text-xs">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1" for="edit-code">Kode Lokasi</label>
                        <input type="text" id="edit-code" readonly class="w-full rounded-xl border border-border px-3 py-2 text-xs bg-gray-50 text-secondary">
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1" for="edit-status">Status</label>
                        <select id="edit-status" class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                            @foreach ($statuses as $st)<option value="{{ $st }}">{{ $st }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1" for="edit-name">Nama OPD</label>
                    <input type="text" id="edit-name" list="opd-options" required class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1" for="edit-address">Alamat</label>
                    <input type="text" id="edit-address" required class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>
                <div class="pt-3 border-t border-border flex items-center justify-end gap-2">
                    <button type="button" class="btn-close-modal px-3.5 py-2 rounded-xl border border-border text-secondary hover:bg-gray-50 font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-blue-700 shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="toast-notification" class="fixed bottom-5 right-5 z-50 hidden max-w-sm rounded-xl bg-dark text-white text-xs px-4 py-3 shadow-xl transition-all"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const list = @json($warehouses);
            const map = {};
            list.forEach(w => { map[w.id] = w; });
            let currentId = list.length ? list[0].id : null;

            const $ = id => document.getElementById(id);
            const BADGES = {
                'Aktif': 'bg-emerald-50 text-emerald-600 border border-emerald-200/60',
                'Maintenance': 'bg-amber-50 text-amber-600 border border-amber-200/60 font-semibold'
            };
            const BADGE_BASE = 'px-2.5 py-0.5 rounded-full text-[11px] font-medium ';
            const BADGE_DEFAULT = 'bg-slate-100 text-slate-500 border border-slate-200/60';

            let toastTimer;
            function showToast(msg) {
                const t = $('toast-notification');
                t.textContent = msg;
                t.classList.remove('hidden');
                clearTimeout(toastTimer);
                toastTimer = setTimeout(() => t.classList.add('hidden'), 2800);
            }

            document.querySelectorAll('[data-dummy-action]').forEach(el => {
                el.addEventListener('click', e => {
                    e.preventDefault(); e.stopPropagation();
                    showToast(el.getAttribute('data-dummy-action') + ' (Aksi UI)');
                });
            });

            // ---- Filter, cari, urut ----
            const rows = Array.from(document.querySelectorAll('.warehouse-row'));
            const tbody = $('warehouse-table-body');
            const emptyState = $('empty-state-row');

            function applyFilters() {
                const q = $('warehouse-search').value.toLowerCase().trim();
                const opd = $('filter-opd').value;
                const st = $('filter-status').value;
                const mode = $('warehouse-sort').value;

                const sorted = rows.slice().sort((a, b) => {
                    if (mode === 'name_asc') return a.dataset.name.localeCompare(b.dataset.name);
                    if (mode === 'assets_desc') return parseInt(b.dataset.assets) - parseInt(a.dataset.assets);
                    return a.dataset.id.localeCompare(b.dataset.id);
                });
                sorted.forEach(r => tbody.insertBefore(r, emptyState));

                let visible = 0;
                sorted.forEach(r => {
                    const ok = (!q || r.dataset.search.includes(q))
                        && (!opd || r.dataset.name === opd)
                        && (!st || r.dataset.status === st);
                    r.classList.toggle('hidden', !ok);
                    if (ok) visible++;
                });
                emptyState.classList.toggle('hidden', visible > 0);
                $('table-info').textContent = `Menampilkan ${visible} dari ${rows.length} lokasi`;
            }
            ['warehouse-search', 'filter-opd', 'filter-status', 'warehouse-sort'].forEach(id => {
                $(id).addEventListener(id === 'warehouse-search' ? 'input' : 'change', applyFilters);
            });
            $('btn-toggle-filters').addEventListener('click', () => {
                $('filter-dropdown-panel').classList.toggle('hidden');
                $('filter-dropdown-panel').classList.toggle('grid');
            });
            $('btn-reset-filters').addEventListener('click', () => {
                $('warehouse-search').value = ''; $('filter-opd').value = ''; $('filter-status').value = ''; $('warehouse-sort').value = 'code_asc';
                applyFilters();
            });
            $('check-all').addEventListener('change', e => {
                document.querySelectorAll('.row-checkbox').forEach(cb => { cb.checked = e.target.checked; });
            });

            // ---- Panel detail ----
            function renderRepairs(items) {
                const box = $('repair-list');
                box.innerHTML = '';
                $('repair-empty').classList.toggle('hidden', items.length > 0);
                items.forEach(r => {
                    const wrap = document.createElement('div');
                    wrap.className = 'relative pl-6 text-xs';
                    const dot = document.createElement('span');
                    dot.className = 'absolute left-0 top-1 w-2.5 h-2.5 rounded-full bg-primary border-2 border-white';
                    const head = document.createElement('div');
                    head.className = 'flex items-center justify-between gap-2';
                    const no = document.createElement('span');
                    no.className = 'font-semibold text-primary';
                    no.textContent = r.ticket || '-';
                    const st = document.createElement('span');
                    st.className = 'px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200/60';
                    st.textContent = r.status || '-';
                    head.append(no, st);
                    const complaint = document.createElement('p');
                    complaint.className = 'text-dark mt-0.5';
                    complaint.textContent = r.complaint || '-';
                    const meta = document.createElement('p');
                    meta.className = 'text-[11px] text-secondary mt-0.5';
                    meta.textContent = `${r.date || '-'} · ${r.technician || '-'}`;
                    wrap.append(dot, head, complaint, meta);
                    box.appendChild(wrap);
                });
            }

            function populateDetail(id) {
                const w = map[id];
                if (!w) return;
                currentId = id;
                $('detail-code').textContent = w.id;
                $('detail-code-2').textContent = w.id;
                $('detail-name').textContent = w.name;
                $('detail-address').textContent = w.location || '-';
                $('detail-assets').textContent = (w.assets_count ?? '-');
                $('detail-status').textContent = w.status;
                const b = $('detail-status-badge');
                b.textContent = w.status;
                b.className = BADGE_BASE + (BADGES[w.status] || BADGE_DEFAULT);
                renderRepairs(Array.isArray(w.repairs) ? w.repairs : []);
                rows.forEach(r => {
                    const on = r.dataset.id === id;
                    r.classList.toggle('bg-blue-50/70', on);
                    r.classList.toggle('border-l-4', on);
                    r.classList.toggle('border-l-primary', on);
                });
            }
            rows.forEach(r => r.addEventListener('click', e => {
                if (e.target.closest('input,button')) return;
                populateDetail(r.dataset.id);
            }));

            // ---- Modal ----
            const modals = ['modal-create-warehouse', 'modal-edit-warehouse'].map($);
            const openModal = m => m.classList.remove('hidden');
            const closeAll = () => modals.forEach(m => m.classList.add('hidden'));
            document.querySelectorAll('.btn-close-modal').forEach(b => b.addEventListener('click', closeAll));
            modals.forEach(m => m.addEventListener('click', e => { if (e.target === m) closeAll(); }));
            document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAll(); });

            $('btn-open-create-modal').addEventListener('click', () => { $('form-create-warehouse').reset(); openModal($('modal-create-warehouse')); });
            $('btn-trigger-edit').addEventListener('click', () => {
                const w = map[currentId];
                if (!w) return showToast('Pilih lokasi terlebih dahulu.');
                $('edit-code').value = w.id;
                $('edit-name').value = w.name;
                $('edit-address').value = w.location || '';
                $('edit-status').value = w.status;
                openModal($('modal-edit-warehouse'));
            });
            $('btn-trigger-delete').addEventListener('click', () => {
                const w = map[currentId];
                if (!w) return showToast('Pilih lokasi terlebih dahulu.');
                if (confirm(`Hapus lokasi "${w.name}" (${w.id})? Data belum terhubung ke backend.`)) {
                    showToast('Hapus Lokasi belum terhubung ke database.');
                }
            });
            [['form-create-warehouse', 'Tambah Lokasi'], ['form-edit-warehouse', 'Edit Lokasi']].forEach(([id, label]) => {
                $(id).addEventListener('submit', e => {
                    e.preventDefault();
                    closeAll();
                    showToast(label + ': belum terhubung ke backend, data belum tersimpan.');
                });
            });

            if (currentId) populateDetail(currentId);
            if (window.lucide) lucide.createIcons();
        });
    </script>
</x-app-layout>
