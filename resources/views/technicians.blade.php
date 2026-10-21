<x-app-layout>

    @php
        // Data dari TechnicianController@index. Jika belum ada, tampil kosong (tanpa data dummy).
        $technicians       = $technicians ?? [];
        $assignableTickets = $assignableTickets ?? [];
        $areas    = $areas ?? collect($technicians)->pluck('area')->filter()->unique()->sort()->values()->all();
        $statuses = ['Available', 'On Task', 'Offline'];
        $statusLabels = ['Available' => 'Tersedia', 'On Task' => 'Sedang Bertugas', 'Offline' => 'Tidak Aktif'];
    @endphp

    <div class="space-y-6">

        {{-- ============================================ --}}
        {{-- 1. HEADER HALAMAN                            --}}
        {{-- ============================================ --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-dark tracking-tight">Technicians</h1>
                <p class="text-secondary text-sm mt-0.5">Kelola teknisi Kominfo dan penugasan tiket ke OPD.</p>
            </div>
            <div>
                <button type="button" id="btn-add-tech"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-white text-sm font-medium hover:bg-blue-700 shadow-sm transition-colors">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Tambah Teknisi</span>
                </button>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- 2. STATISTIK (dihitung dari data dummy)       --}}
        {{-- ============================================ --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Total Teknisi</p>
                    <p id="stat-total" class="text-2xl font-bold text-dark mt-0.5">0</p>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i data-lucide="user-check" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Teknisi Tersedia</p>
                    <p id="stat-active" class="text-2xl font-bold text-dark mt-0.5">0</p>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <i data-lucide="wrench" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Sedang Bertugas</p>
                    <p id="stat-ontask" class="text-2xl font-bold text-dark mt-0.5">0</p>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <i data-lucide="ticket" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Tiket Ditangani</p>
                    <p id="stat-offline" class="text-2xl font-bold text-dark mt-0.5">0</p>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- 3. TABEL (KIRI) + PANEL DETAIL (KANAN)        --}}
        {{-- ============================================ --}}
        <div class="flex flex-col xl:flex-row items-start gap-5">

            {{-- ======== TABEL TEKNISI ======== --}}
            <div class="w-full min-w-0 xl:flex-1 bg-white rounded-2xl border border-border shadow-[0_2px_6px_rgba(0,0,0,0.02)] overflow-hidden">

                {{-- Search & Filter --}}
                <div class="p-4 border-b border-border">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="relative flex-1 min-w-[220px]">
                            <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                            <input id="tech-search" type="text" autocomplete="off"
                                   placeholder="Cari nama teknisi, kode, atau OPD tujuan..."
                                   class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-border bg-surface focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        </div>

                        <select id="filter-status" aria-label="Filter status"
                                class="text-xs rounded-xl border border-border bg-white px-3 py-2 text-dark focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <option value="">Semua Status</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}">{{ $statusLabels[$status] ?? $status }}</option>
                            @endforeach
                        </select>

                        <select id="filter-area" aria-label="Filter area"
                                class="text-xs rounded-xl border border-border bg-white px-3 py-2 text-dark focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <option value="">Semua Area</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area }}">{{ $area }}</option>
                            @endforeach
                        </select>

                        <button type="button" id="btn-reset-filters"
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-border text-xs font-medium text-secondary hover:bg-gray-50 transition-colors">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                            <span>Reset</span>
                        </button>
                    </div>
                </div>

                {{-- Tabel --}}
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[680px] text-xs text-left">
                        <thead>
                            <tr class="text-secondary bg-[#FAFCFF] border-b border-border font-medium text-[11px] uppercase tracking-wider">
                                <th class="pl-4 py-3 w-8">
                                    <input id="check-all" type="checkbox" aria-label="Pilih semua teknisi di halaman ini"
                                           class="rounded border-gray-300 text-primary focus:ring-primary/20">
                                </th>
                                <th class="py-3 pr-3">Nama Teknisi</th>
                                <th class="py-3 pr-3 whitespace-nowrap">Kode Teknisi</th>
                                <th class="py-3 pr-3">Status</th>
                                <th class="py-3 pr-3 whitespace-nowrap">Tiket Aktif</th>
                                <th class="py-3 pr-3">OPD Tujuan</th>
                                <th class="py-3 pr-4 text-center w-12">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tech-table-body" class="divide-y divide-gray-100"></tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="p-3.5 border-t border-border flex flex-wrap items-center justify-between gap-3 text-xs">
                    <p id="table-info" class="text-secondary">Menampilkan 0 dari 0 teknisi</p>
                    <div id="pagination" class="flex items-center gap-1"></div>
                </div>
            </div>

            {{-- Backdrop drawer (tablet / mobile) --}}
            <div id="tech-backdrop" class="hidden fixed inset-0 z-30 bg-black/40 xl:hidden"></div>

            {{-- ======== PANEL DETAIL ======== --}}
            <aside id="tech-panel"
                   class="hidden fixed inset-y-0 right-0 z-40 w-full max-w-[560px] overflow-y-auto bg-white p-5 shadow-2xl
                          xl:sticky xl:top-20 xl:bottom-auto xl:right-auto xl:z-auto xl:w-[480px] 2xl:w-[520px] xl:max-w-none xl:shrink-0
                          xl:overflow-visible xl:rounded-2xl xl:border xl:border-border xl:shadow-[0_2px_6px_rgba(0,0,0,0.02)]"
                   aria-label="Detail teknisi">

                {{-- Header --}}
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-4 min-w-0">
                        <div id="d-avatar" class="shrink-0"></div>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 id="d-name" class="text-lg font-bold text-dark"></h2>
                                <span id="d-badge"></span>
                            </div>
                            <p id="d-role" class="text-sm text-dark mt-1"></p>
                            <p class="text-xs text-secondary mt-1">Kode Unik: <span id="d-nik" class="font-semibold text-primary"></span></p>
                        </div>
                    </div>
                    <button type="button" id="btn-close-panel" title="Tutup panel"
                            class="p-1.5 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100 transition-colors" aria-label="Tutup panel detail">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                {{-- Ringkasan 3 kartu --}}
                <div class="grid grid-cols-3 gap-2 mt-4 p-3 rounded-xl bg-surface border border-border/80 text-xs">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0"><i data-lucide="map-pin" class="w-4 h-4"></i></span>
                        <div class="min-w-0">
                            <p class="text-[10px] text-secondary">Area</p>
                            <p id="d-area" class="font-semibold text-dark truncate"></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0"><i data-lucide="ticket" class="w-4 h-4"></i></span>
                        <div class="min-w-0">
                            <p class="text-[10px] text-secondary">Tiket Aktif</p>
                            <p id="d-tickets" class="font-semibold text-dark"></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center shrink-0"><i data-lucide="star" class="w-4 h-4"></i></span>
                        <div class="min-w-0">
                            <p class="text-[10px] text-secondary">Rating</p>
                            <p id="d-rating" class="font-semibold text-dark"></p>
                        </div>
                    </div>
                </div>

                {{-- Tab --}}
                <div id="panel-tabs" class="flex flex-wrap border-b border-border text-xs font-semibold mt-4 mb-4 gap-x-6" role="tablist">
                    <button type="button" role="tab" class="tab-btn pb-2.5 border-b-2 border-primary text-primary transition-colors" data-tab="detail">Detail</button>
                    <button type="button" role="tab" class="tab-btn pb-2.5 border-b-2 border-transparent text-secondary hover:text-dark transition-colors" data-tab="tickets">Tiket Ditugaskan</button>
                    <button type="button" role="tab" class="tab-btn pb-2.5 border-b-2 border-transparent text-secondary hover:text-dark transition-colors" data-tab="history">Riwayat Perbaikan</button>
                    <button type="button" role="tab" class="tab-btn pb-2.5 border-b-2 border-transparent text-secondary hover:text-dark transition-colors" data-tab="schedule">Jadwal</button>
                </div>

                {{-- TAB: DETAIL --}}
                <div id="pane-detail" class="tab-pane">
                    <div class="p-3.5 rounded-xl border border-border bg-white text-xs space-y-2.5">
                        <p class="font-semibold text-dark text-sm">Informasi Teknisi</p>
                        <div class="grid grid-cols-2 gap-x-4 gap-y-2.5">
                            <div><p class="text-secondary text-[11px]">Kode Unik</p><p id="i-nik" class="font-medium text-dark"></p></div>
                            <div><p class="text-secondary text-[11px]">Nama Lengkap</p><p id="i-name" class="font-medium text-dark"></p></div>
                            <div><p class="text-secondary text-[11px]">Jabatan</p><p id="i-role" class="font-medium text-dark"></p></div>
                            <div><p class="text-secondary text-[11px] mb-0.5">Status</p><span id="i-status"></span></div>
                            <div><p class="text-secondary text-[11px]">Telepon</p><p id="i-phone" class="font-medium text-dark"></p></div>
                            <div><p class="text-secondary text-[11px]">Email</p><p id="i-email" class="font-medium text-dark break-all"></p></div>
                            <div><p class="text-secondary text-[11px]">Area</p><p id="i-area" class="font-medium text-dark"></p></div>
                            <div><p class="text-secondary text-[11px]">OPD Tujuan</p><p id="i-opd" class="font-medium text-dark"></p></div>
                            <div><p class="text-secondary text-[11px]">Bergabung Sejak</p><p id="i-joined" class="font-medium text-dark"></p></div>
                            <div><p class="text-secondary text-[11px]">Pengalaman</p><p id="i-exp" class="font-medium text-dark"></p></div>
                        </div>
                    </div>
                    <div class="p-3.5 mt-3 rounded-xl border border-border bg-white text-xs space-y-2">
                        <p class="font-semibold text-dark text-sm">Ubah Status</p>
                        <select id="action-status" aria-label="Ubah status teknisi"
                                class="w-full text-xs rounded-lg border border-border bg-white px-3 py-2 text-dark focus:outline-none focus:ring-2 focus:ring-primary/20">
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}">{{ $statusLabels[$status] ?? $status }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- TAB: TIKET DITUGASKAN --}}
                <div id="pane-tickets" class="tab-pane hidden text-xs">
                    <p class="font-semibold text-dark text-sm mb-2.5">Tiket Ditugaskan</p>
                    <ul id="ticket-list" class="space-y-2"></ul>
                </div>

                {{-- TAB: RIWAYAT PERBAIKAN --}}
                <div id="pane-history" class="tab-pane hidden text-xs">
                    <p class="font-semibold text-dark text-sm mb-2.5">Riwayat Perbaikan</p>
                    <div id="history-list" class="space-y-3 relative before:absolute before:top-1 before:bottom-1 before:left-[5px] before:w-0.5 before:bg-gray-200"></div>
                </div>

                {{-- TAB: JADWAL --}}
                <div id="pane-schedule" class="tab-pane hidden text-xs">
                    <p class="font-semibold text-dark text-sm mb-2.5">Jadwal</p>
                    <ul id="schedule-list" class="space-y-2"></ul>
                </div>

                {{-- Tombol aksi --}}
                <div class="grid grid-cols-2 gap-2 mt-4 pt-4 border-t border-border">
                    <button type="button" id="btn-assign"
                            class="inline-flex items-center justify-center gap-2 py-2 px-3 rounded-lg bg-primary text-white text-xs font-semibold hover:bg-blue-700 shadow-sm transition-colors">
                        <i data-lucide="user-plus" class="w-4 h-4"></i> Assign Ticket
                    </button>
                    <button type="button" id="btn-location"
                            class="inline-flex items-center justify-center gap-2 py-2 px-3 rounded-lg bg-white border border-border text-primary text-xs font-medium hover:bg-blue-50 transition-colors">
                        <i data-lucide="map-pin" class="w-4 h-4"></i> Lihat Lokasi OPD
                    </button>
                </div>
            </aside>
        </div>
    </div>

    {{-- Menu aksi baris (satu elemen, diposisikan lewat JavaScript) --}}
    <div id="row-menu" class="hidden fixed z-50 w-44 rounded-xl border border-border bg-white shadow-xl py-1 text-xs">
        <button type="button" data-act="detail" class="w-full flex items-center gap-2 px-3 py-2 text-left text-dark hover:bg-gray-50"><i data-lucide="eye" class="w-3.5 h-3.5 text-secondary"></i> Lihat Detail</button>
        <button type="button" data-act="edit" class="w-full flex items-center gap-2 px-3 py-2 text-left text-dark hover:bg-gray-50"><i data-lucide="pencil" class="w-3.5 h-3.5 text-secondary"></i> Edit Teknisi</button>
        <button type="button" data-act="assign" class="w-full flex items-center gap-2 px-3 py-2 text-left text-dark hover:bg-gray-50"><i data-lucide="user-plus" class="w-3.5 h-3.5 text-secondary"></i> Assign Ticket</button>
        <button type="button" data-act="toggle" class="w-full flex items-center gap-2 px-3 py-2 text-left text-danger hover:bg-red-50 border-t border-gray-100"><i data-lucide="x-circle" class="w-3.5 h-3.5"></i> <span data-role="toggle-label">Nonaktifkan</span></button>
    </div>

    {{-- Modal-modal Technicians --}}
    <x-technician-modals :areas="$areas" :statuses="$statuses" />

    {{-- Toast --}}
    <div id="toast-notification" role="status"
         class="fixed bottom-5 right-5 z-[70] hidden max-w-sm rounded-xl bg-dark text-white text-xs px-4 py-3 shadow-xl"></div>

    {{-- Template badge: HTML badge diambil dari komponen Blade, dipakai ulang oleh JavaScript --}}
    <div class="hidden" aria-hidden="true">
        @foreach (['High', 'Normal', 'Low'] as $priority)
            <template data-badge-priority="{{ $priority }}"><x-priority-badge :priority="$priority" /></template>
        @endforeach
    </div>

    {{-- ============================================ --}}
    {{-- JAVASCRIPT INTERAKTIF (simulasi frontend)     --}}
    {{-- ============================================ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ---------- State (hanya di memori browser, tidak disimpan ke database) ----------
            const state = {
                data: @json($technicians),
                pool: @json($assignableTickets),
                selectedId: null,
                page: 1,
                perPage: 10,
                search: '',
                status: '',
                area: '',
                checked: new Set(),
                nextId: 1,
                tab: 'detail',
                formMode: 'add',
                menuId: null,
            };

            const $ = (id) => document.getElementById(id);

            // Normalisasi data: kode unik (preview), penugasan, riwayat perbaikan, jadwal.
            // Kode "TK001" di sini hanya turunan dari id untuk preview. Kode asli harus dibuat di backend.
            const pad = (n) => String(n).padStart(3, '0');
            const TODAY = new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            function normalize(t) {
                t.code = t.code || ('TK' + pad(t.id));
                t.active = (t.active || []).map((k) => Object.assign({ opd: '-', address: '-', date: '-', work_status: 'Ditugaskan' }, k));
                t.repairs = t.repairs || [];
                t.schedule = t.schedule || [];
                t.history = t.history || [];
                t.tickets = t.tickets != null ? t.tickets : t.active.length;
                return t;
            }
            state.data.forEach(normalize);
            state.nextId = state.data.reduce((m, t) => Math.max(m, Number(t.id) || 0), 0) + 1;
            state.selectedId = state.data.length ? state.data[0].id : null;

            const STATUS_UI = {
                'Available': ['Tersedia', 'bg-emerald-50 text-emerald-600 border border-emerald-200/60'],
                'On Task':   ['Sedang Bertugas', 'bg-amber-50 text-amber-600 border border-amber-200/60'],
                'Offline':   ['Tidak Aktif', 'bg-slate-100 text-slate-500 border border-slate-200/60'],
            };
            const WORK_UI = {
                'Ditugaskan':      'bg-blue-50 text-blue-600 border border-blue-200/60',
                'Diterima':        'bg-indigo-50 text-indigo-600 border border-indigo-200/60',
                'Menuju Lokasi':   'bg-purple-50 text-purple-600 border border-purple-200/60',
                'Sedang Perbaikan':'bg-amber-50 text-amber-600 border border-amber-200/60',
                'Selesai':         'bg-emerald-50 text-emerald-600 border border-emerald-200/60',
            };
            const pill = (text, cls) => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium whitespace-nowrap ' + (cls || 'bg-gray-100 text-gray-600 border border-gray-200') + '">' + esc(text) + '</span>';
            const statusBadgeUi = (st) => { const u = STATUS_UI[st]; return u ? pill(u[0], u[1]) : pill(st); };
            const workBadge = (w) => pill(w, WORK_UI[w]);
            const opdOf = (t) => {
                const names = [...new Set(t.active.map((k) => k.opd).filter((n) => n && n !== '-'))];
                return names.length ? names[0] + (names.length > 1 ? ' +' + (names.length - 1) : '') : '-';
            };
            const MONTHS = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const AVATAR_COLORS = [
                'bg-blue-100 text-blue-700', 'bg-emerald-100 text-emerald-700', 'bg-amber-100 text-amber-700',
                'bg-rose-100 text-rose-700', 'bg-violet-100 text-violet-700', 'bg-cyan-100 text-cyan-700',
            ];

            // ---------- Util ----------
            const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

            const badgeTemplates = {};
            document.querySelectorAll('template[data-badge-status]').forEach((t) => { badgeTemplates['s:' + t.dataset.badgeStatus] = t.innerHTML; });
            document.querySelectorAll('template[data-badge-priority]').forEach((t) => { badgeTemplates['p:' + t.dataset.badgePriority] = t.innerHTML; });
            const statusBadge = (s) => statusBadgeUi(s);
            const priorityBadge = (p) => badgeTemplates['p:' + p] || esc(p);

            function fmtDate(iso) {
                if (!iso) return '-';
                const [y, m, d] = iso.split('-');
                return parseInt(d, 10) + ' ' + MONTHS[parseInt(m, 10) - 1] + ' ' + y;
            }

            function initials(name) {
                const p = String(name).trim().split(/\s+/);
                const first = (p[0] || '').charAt(0);
                const last = p.length > 1 ? p[p.length - 1].charAt(0) : '';
                return (first + last).toUpperCase();
            }

            function avatar(t, size) {
                if (t.photo) return '<img src="' + esc(t.photo) + '" alt="' + esc(t.name) + '" class="' + size + ' shrink-0 rounded-full object-cover border border-border">';
                const color = AVATAR_COLORS[t.id % AVATAR_COLORS.length];
                return '<span class="' + size + ' shrink-0 rounded-full flex items-center justify-center font-semibold ' + color + '">' + esc(initials(t.name)) + '</span>';
            }

            const ratingText = (t) => (t.rating == null ? '–' : Number(t.rating).toFixed(1));
            const current = () => state.data.find((t) => t.id === state.selectedId) || null;
            const refreshIcons = () => { if (window.lucide) window.lucide.createIcons(); };

            let toastTimer;
            function showToast(message) {
                const toast = $('toast-notification');
                toast.textContent = message;
                toast.classList.remove('hidden');
                clearTimeout(toastTimer);
                toastTimer = setTimeout(() => toast.classList.add('hidden'), 2600);
            }

            // ---------- Filter ----------
            function filtered() {
                const q = state.search.toLowerCase().trim();
                return state.data.filter((t) => {
                    const hay = (t.name + ' ' + t.code + ' ' + opdOf(t) + ' ' + t.area + ' ' + t.role).toLowerCase();
                    return (!q || hay.includes(q))
                        && (!state.status || t.status === state.status)
                        && (!state.area || t.area === state.area);
                });
            }

            // ---------- Statistik ----------
            function renderStats() {
                const count = (s) => state.data.filter((t) => t.status === s).length;
                const onTask = count('On Task');
                const available = count('Available');
                $('stat-total').textContent = state.data.length;
                $('stat-active').textContent = available;
                $('stat-ontask').textContent = onTask;
                $('stat-offline').textContent = state.data.reduce((n, t) => n + (t.tickets || 0), 0);
            }

            // ---------- Tabel ----------
            function renderTable() {
                const list = filtered();
                const pages = Math.max(1, Math.ceil(list.length / state.perPage));
                state.page = Math.min(Math.max(1, state.page), pages);
                const start = (state.page - 1) * state.perPage;
                const slice = list.slice(start, start + state.perPage);

                $('tech-table-body').innerHTML = slice.length
                    ? slice.map((t) => {
                        const selected = t.id === state.selectedId;
                        return '<tr class="tech-row cursor-pointer transition-colors hover:bg-blue-50/40 ' + (selected ? 'bg-blue-50/70 border-l-4 border-l-primary' : '') + '" data-id="' + t.id + '">'
                            + '<td class="pl-4 py-3 w-8"><input type="checkbox" class="row-check rounded border-gray-300 text-primary focus:ring-primary/20" data-id="' + t.id + '" aria-label="Pilih ' + esc(t.name) + '"' + (state.checked.has(t.id) ? ' checked' : '') + '></td>'
                            + '<td class="py-3 pr-3"><div class="flex items-center gap-3">' + avatar(t, 'w-9 h-9 text-xs')
                            + '<div class="min-w-0"><p class="font-semibold text-primary whitespace-nowrap">' + esc(t.name) + '</p><p class="text-[11px] text-secondary">' + esc(t.role) + '</p></div></div></td>'
                            + '<td class="py-3 pr-3 text-dark font-medium whitespace-nowrap">' + esc(t.code) + '</td>'
                            + '<td class="py-3 pr-3">' + statusBadge(t.status) + '</td>'
                            + '<td class="py-3 pr-3 text-dark font-semibold">' + t.tickets + '</td>'
                            + '<td class="py-3 pr-3 text-secondary">' + esc(opdOf(t)) + '</td>'
                            + '<td class="py-3 pr-4 text-center"><button type="button" class="row-menu-btn p-1 rounded-md text-gray-400 hover:text-dark hover:bg-gray-100" data-id="' + t.id + '" aria-label="Aksi ' + esc(t.name) + '"><i data-lucide="more-vertical" class="w-4 h-4"></i></button></td>'
                            + '</tr>';
                    }).join('')
                    : '<tr><td colspan="7" class="py-12 text-center text-gray-400"><i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>Tidak ada teknisi yang cocok dengan pencarian / filter.</td></tr>';

                // Info + checkbox "pilih semua"
                const from = list.length ? start + 1 : 0;
                const to = start + slice.length;
                const picked = state.checked.size;
                $('table-info').textContent = 'Menampilkan ' + from + '–' + to + ' dari ' + list.length + ' teknisi' + (picked ? ' · ' + picked + ' dipilih' : '');

                const checkedOnPage = slice.filter((t) => state.checked.has(t.id)).length;
                const all = $('check-all');
                all.checked = slice.length > 0 && checkedOnPage === slice.length;
                all.indeterminate = checkedOnPage > 0 && checkedOnPage < slice.length;

                // Pagination
                let html = '<button type="button" data-page="' + (state.page - 1) + '" ' + (state.page <= 1 ? 'disabled' : '') + ' aria-label="Halaman sebelumnya" class="w-7 h-7 rounded-lg border border-border flex items-center justify-center text-secondary hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed"><i data-lucide="chevron-left" class="w-3.5 h-3.5"></i></button>';
                for (let p = 1; p <= pages; p++) {
                    html += p === state.page
                        ? '<button type="button" class="w-7 h-7 rounded-lg bg-primary text-white font-medium flex items-center justify-center" aria-current="page">' + p + '</button>'
                        : '<button type="button" data-page="' + p + '" class="w-7 h-7 rounded-lg border border-border text-secondary hover:bg-gray-50 flex items-center justify-center">' + p + '</button>';
                }
                html += '<button type="button" data-page="' + (state.page + 1) + '" ' + (state.page >= pages ? 'disabled' : '') + ' aria-label="Halaman berikutnya" class="w-7 h-7 rounded-lg border border-border flex items-center justify-center text-secondary hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed"><i data-lucide="chevron-right" class="w-3.5 h-3.5"></i></button>';
                $('pagination').innerHTML = html;

                refreshIcons();
            }

            // ---------- Panel detail ----------
            function renderDetail() {
                const t = current();
                if (!t) return;

                $('d-avatar').innerHTML = avatar(t, 'w-20 h-20 text-2xl');
                $('d-name').textContent = t.name;
                $('d-badge').innerHTML = statusBadge(t.status);
                $('d-role').textContent = t.role;
                $('d-nik').textContent = t.code;
                $('d-area').textContent = t.area;
                $('d-tickets').textContent = t.tickets;
                $('d-rating').textContent = ratingText(t);

                $('i-name').textContent = t.name;
                $('i-role').textContent = t.role;
                $('i-nik').textContent = t.code;
                $('i-phone').textContent = t.phone;
                $('i-email').textContent = t.email;
                $('i-area').textContent = t.area;
                $('i-opd').textContent = opdOf(t);
                $('i-status').innerHTML = statusBadge(t.status);
                $('i-joined').textContent = fmtDate(t.joined_iso);
                $('i-exp').textContent = t.exp_years + ' Tahun';
                $('action-status').value = t.status;

                // Tiket Ditugaskan
                $('ticket-list').innerHTML = t.active.length
                    ? t.active.map((k) => '<li class="p-3 rounded-xl border border-border bg-surface space-y-1">'
                        + '<div class="flex items-center justify-between gap-2"><span class="font-semibold text-primary">' + esc(k.id) + '</span>' + workBadge(k.work_status) + '</div>'
                        + '<p class="text-dark">' + esc(k.title) + '</p>'
                        + '<p class="text-[11px] text-secondary"><span class="font-medium text-dark">' + esc(k.opd) + '</span> · ' + esc(k.address) + '</p>'
                        + '<div class="flex items-center justify-between"><span class="text-[11px] text-gray-400">Ditugaskan: ' + esc(k.date) + '</span>'
                        + '<button type="button" class="ticket-loc text-primary text-[11px] font-semibold hover:underline" data-ticket="' + esc(k.id) + '">Lihat Lokasi OPD</button></div></li>').join('')
                    : '<li class="py-8 text-center text-secondary">Belum ada tiket yang ditugaskan.</li>';

                // Riwayat Perbaikan
                $('history-list').innerHTML = t.repairs.length
                    ? t.repairs.map((h, i) => '<div class="relative flex items-start gap-3 pl-6">'
                        + '<span class="absolute left-0 top-1 w-3 h-3 rounded-full ring-4 ring-white ' + (i === 0 ? 'bg-primary' : 'bg-gray-300') + '"></span>'
                        + '<div class="flex-1 min-w-0"><div class="flex items-center justify-between gap-2"><p class="font-semibold text-primary">' + esc(h.ticket) + '</p>' + pill(h.status || 'Selesai', WORK_UI['Selesai']) + '</div>'
                        + '<p class="text-dark">' + esc(h.opd) + '</p><p class="text-secondary">' + esc(h.type) + '</p>'
                        + '<p class="text-[11px] text-gray-400 mt-0.5">' + esc(h.date) + '</p></div></div>').join('')
                    : '<div class="py-8 text-center text-secondary pl-4">Belum ada riwayat perbaikan.</div>';

                // Jadwal
                $('schedule-list').innerHTML = t.schedule.length
                    ? t.schedule.map((j) => '<li class="flex items-center justify-between gap-3 p-2.5 rounded-xl border border-border bg-surface"><span class="font-semibold w-16">' + esc(j.day) + '</span><span class="flex-1">' + esc(j.note || '-') + '</span><span class="text-secondary">' + esc(j.time || '-') + '</span></li>').join('')
                    : '<li class="py-8 text-center text-secondary">Jadwal belum tersedia.</li>';

                refreshIcons();
            }

            function setTab(name) {
                state.tab = name;
                document.querySelectorAll('#panel-tabs .tab-btn').forEach((b) => {
                    const on = b.dataset.tab === name;
                    b.classList.toggle('border-primary', on);
                    b.classList.toggle('text-primary', on);
                    b.classList.toggle('border-transparent', !on);
                    b.classList.toggle('text-secondary', !on);
                    b.setAttribute('aria-selected', on ? 'true' : 'false');
                });
                document.querySelectorAll('.tab-pane').forEach((p) => p.classList.toggle('hidden', p.id !== 'pane-' + name));
            }

            const isDesktop = () => window.innerWidth >= 1280;

            function showPanel() {
                $('tech-panel').classList.remove('hidden');
                if (!isDesktop()) {
                    $('tech-backdrop').classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                }
            }

            function hidePanel() {
                $('tech-panel').classList.add('hidden');
                $('tech-backdrop').classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            const panelOpen = () => !$('tech-panel').classList.contains('hidden');

            function selectTech(id, open) {
                state.selectedId = id;
                renderTable();
                renderDetail();
                if (open !== false) showPanel();
            }

            function refreshAll() {
                renderStats();
                renderTable();
                renderDetail();
            }

            // ---------- Modal helper ----------
            function openModal(id) {
                closeRowMenu();
                const el = $(id);
                el.classList.remove('hidden');
                const first = el.querySelector('input:not([type=radio]), select, button[type=submit], #confirm-ok');
                if (first) setTimeout(() => first.focus(), 0);
                refreshIcons();
            }

            function closeModal(id) { $(id).classList.add('hidden'); }

            function closeTopModal() {
                const open = Array.from(document.querySelectorAll('.tech-modal')).filter((m) => !m.classList.contains('hidden'));
                if (!open.length) return false;
                open[open.length - 1].classList.add('hidden');
                return true;
            }

            document.querySelectorAll('.tech-modal').forEach((m) => {
                m.addEventListener('mousedown', (e) => { if (e.target === m) m.classList.add('hidden'); });
                m.querySelectorAll('[data-close-modal]').forEach((b) => b.addEventListener('click', () => m.classList.add('hidden')));
            });

            // ---------- Menu aksi baris ----------
            function openRowMenu(btn, id) {
                state.menuId = id;
                const t = state.data.find((x) => x.id === id);
                const menu = $('row-menu');
                menu.querySelector('[data-role="toggle-label"]').textContent = t && t.status === 'Offline' ? 'Aktifkan' : 'Nonaktifkan';
                menu.classList.remove('hidden');
                const r = btn.getBoundingClientRect();
                const h = menu.offsetHeight || 170;
                const top = (r.bottom + 4 + h > window.innerHeight) ? Math.max(8, r.top - h - 4) : r.bottom + 4;
                menu.style.top = top + 'px';
                menu.style.left = Math.max(8, r.right - 176) + 'px';
                refreshIcons();
            }

            function closeRowMenu() { $('row-menu').classList.add('hidden'); state.menuId = null; }

            // ---------- Aksi: tambah / edit ----------
            function openForm(mode) {
                state.formMode = mode;
                const t = mode === 'edit' ? current() : null;
                $('form-tech').reset();
                $('f-nik').setCustomValidity('');
                $('form-title').textContent = mode === 'add' ? 'Tambah Teknisi' : 'Edit Teknisi';
                $('form-sub').textContent = mode === 'add' ? 'Isi data teknisi baru' : 'Ubah data ' + (t ? t.name : 'teknisi');
                $('form-submit').textContent = mode === 'add' ? 'Simpan Teknisi' : 'Simpan Perubahan';
                if (t) {
                    $('f-name').value = t.name; $('f-nik').value = t.code; $('f-role').value = t.role;
                    $('f-phone').value = t.phone; $('f-email').value = t.email; $('f-area').value = t.area;
                    $('f-status').value = t.status; $('f-joined').value = t.joined_iso; $('f-exp').value = t.exp_years;
                } else {
                    $('f-nik').value = 'TK' + pad(state.nextId);
                    $('f-role').value = 'Teknisi';
                    $('f-area').selectedIndex = 0;
                    $('f-status').value = 'Available';
                    $('f-joined').value = new Date().toISOString().slice(0, 10);
                    $('f-exp').value = 0;
                }
                openModal('modal-form');
            }

            $('f-nik').addEventListener('input', () => $('f-nik').setCustomValidity(''));

            $('form-tech').addEventListener('submit', (e) => {
                e.preventDefault();
                const code = $('f-nik').value.trim().toUpperCase();
                const dup = state.data.some((x) => x.code.toLowerCase() === code.toLowerCase() && !(state.formMode === 'edit' && x.id === state.selectedId));
                $('f-nik').setCustomValidity(dup ? 'Kode teknisi sudah digunakan teknisi lain.' : '');
                if (!e.target.reportValidity()) return;

                const values = {
                    name: $('f-name').value.trim(), code: code, role: $('f-role').value.trim(),
                    phone: $('f-phone').value.trim(), email: $('f-email').value.trim(), area: $('f-area').value,
                    status: $('f-status').value, joined_iso: $('f-joined').value, exp_years: parseInt($('f-exp').value, 10) || 0,
                };

                if (state.formMode === 'edit') {
                    const t = current();
                    if (t.status !== values.status) {
                        t.history.unshift({ title: 'Status diubah', desc: t.status + ' → ' + values.status, time: 'Baru saja' });
                    }
                    Object.assign(t, values);
                    closeModal('modal-form');
                    refreshAll();
                    showToast('Data teknisi diperbarui (simulasi frontend).');
                } else {
                    const t = Object.assign({
                        id: state.nextId++, tickets: 0, rating: null, active: [], repairs: [], schedule: [],
                        history: [{ title: 'Teknisi ditambahkan', desc: 'Data teknisi dibuat (simulasi)', time: 'Baru saja' }],
                    }, values);
                    state.data.push(t);
                    state.search = ''; state.status = ''; state.area = '';
                    $('tech-search').value = ''; $('filter-status').value = ''; $('filter-area').value = '';
                    state.page = Math.ceil(state.data.length / state.perPage);
                    closeModal('modal-form');
                    state.selectedId = t.id;
                    renderStats();
                    selectTech(t.id, true);
                    showToast('Teknisi baru ditambahkan (simulasi frontend).');
                }
            });

            // ---------- Aksi: assign ticket ----------
            function openAssign() {
                const t = current();
                if (!t) return;
                $('assign-name').textContent = t.name;
                const blocked = t.status === 'Offline' || t.status === 'Cuti';
                const warn = $('assign-warning');
                warn.classList.toggle('hidden', !blocked);
                warn.textContent = blocked ? 'Teknisi berstatus ' + t.status + ' dan tidak dapat menerima ticket baru.' : '';
                $('assign-list').innerHTML = state.pool.length
                    ? state.pool.map((k) => '<label class="flex items-center gap-3 p-3 rounded-xl border border-border bg-surface cursor-pointer hover:border-primary/50 ' + (blocked ? 'opacity-60 pointer-events-none' : '') + '">'
                        + '<input type="radio" name="assign-ticket" value="' + esc(k.id) + '" class="text-primary focus:ring-primary/20"' + (blocked ? ' disabled' : '') + '>'
                        + '<div class="flex-1 min-w-0"><p class="font-semibold text-dark">' + esc(k.id) + ' — ' + esc(k.title) + '</p><p class="text-[11px] text-secondary">' + esc(k.customer) + ' · ' + esc(k.location) + '</p></div>'
                        + priorityBadge(k.priority) + '</label>').join('')
                    : '<p class="py-6 text-center text-secondary">Tidak ada ticket yang menunggu penugasan.</p>';
                $('assign-confirm').disabled = true;
                openModal('modal-assign');
            }

            $('assign-list').addEventListener('change', (e) => {
                if (e.target.name === 'assign-ticket') $('assign-confirm').disabled = false;
            });

            $('form-assign').addEventListener('submit', (e) => {
                e.preventDefault();
                const picked = document.querySelector('input[name="assign-ticket"]:checked');
                const t = current();
                if (!picked || !t) return;
                const idx = state.pool.findIndex((k) => k.id === picked.value);
                if (idx < 0) return;
                const k = state.pool.splice(idx, 1)[0];
                t.active.unshift({ id: k.id, title: k.title, priority: k.priority, opd: k.customer || '-', address: k.area || k.location || '-', date: TODAY, work_status: 'Ditugaskan' });
                t.tickets += 1;
                if (t.status === 'Available') t.status = 'On Task';
                t.history.unshift({ title: 'Ticket ditugaskan', desc: k.id + ' — ' + k.title, time: 'Baru saja' });
                closeModal('modal-assign');
                refreshAll();
                showToast(k.id + ' ditugaskan kepada ' + t.name + ' (simulasi frontend).');
            });

            // ---------- Aksi: lokasi & jadwal ----------
            function openLocation(ticketId) {
                const t = current();
                if (!t) return;
                const k = t.active.find((x) => x.id === ticketId) || t.active[0];
                if (!k) { showToast('Teknisi ini belum memiliki tiket yang ditugaskan.'); return; }
                $('location-sub').textContent = k.id + ' · ' + k.opd;
                $('location-area').textContent = k.address;
                $('location-updated').textContent = 'Berdasarkan alamat OPD';
                $('location-pin').classList.add('hidden');
                const offBox = $('location-off');
                offBox.classList.remove('hidden');
                offBox.classList.add('flex');
                offBox.textContent = 'Peta belum tersedia. Alamat: ' + k.address;
                openModal('modal-location');
            }

            // ---------- Aksi: nonaktifkan / aktifkan ----------
            function openConfirm() {
                const t = current();
                if (!t) return;
                const reactivate = t.status === 'Offline';
                const icon = $('confirm-icon');
                icon.className = 'mx-auto w-12 h-12 rounded-full flex items-center justify-center ' + (reactivate ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-danger');
                $('confirm-title').textContent = (reactivate ? 'Aktifkan ' : 'Nonaktifkan ') + t.name + '?';
                $('confirm-text').textContent = reactivate
                    ? 'Status teknisi akan diubah menjadi Available (simulasi frontend).'
                    : 'Status teknisi akan diubah menjadi Offline (simulasi frontend, data tidak disimpan).';
                const warn = $('confirm-warning');
                const showWarn = !reactivate && t.tickets > 0;
                warn.classList.toggle('hidden', !showWarn);
                warn.textContent = showWarn ? 'Teknisi ini masih memiliki ' + t.tickets + ' ticket aktif.' : '';
                const ok = $('confirm-ok');
                ok.textContent = reactivate ? 'Ya, Aktifkan' : 'Ya, Nonaktifkan';
                ok.className = 'px-4 py-2 rounded-xl text-white font-semibold shadow-sm ' + (reactivate ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-danger hover:bg-red-600');
                openModal('modal-confirm');
            }

            $('confirm-ok').addEventListener('click', () => {
                const t = current();
                if (!t) return;
                const reactivate = t.status === 'Offline';
                const next = reactivate ? 'Available' : 'Offline';
                t.history.unshift({ title: reactivate ? 'Teknisi diaktifkan' : 'Teknisi dinonaktifkan', desc: t.status + ' → ' + next, time: 'Baru saja' });
                t.status = next;
                closeModal('modal-confirm');
                refreshAll();
                showToast(t.name + (reactivate ? ' diaktifkan' : ' dinonaktifkan') + ' (simulasi frontend).');
            });

            // ---------- Event: toolbar ----------
            $('btn-add-tech').addEventListener('click', () => openForm('add'));

            $('tech-search').addEventListener('input', (e) => { state.search = e.target.value; state.page = 1; renderTable(); });
            $('filter-status').addEventListener('change', (e) => { state.status = e.target.value; state.page = 1; renderTable(); });
            $('filter-area').addEventListener('change', (e) => { state.area = e.target.value; state.page = 1; renderTable(); });
            $('btn-reset-filters').addEventListener('click', () => {
                state.search = ''; state.status = ''; state.area = ''; state.page = 1;
                $('tech-search').value = ''; $('filter-status').value = ''; $('filter-area').value = '';
                renderTable();
            });

            // ---------- Event: tabel ----------
            $('check-all').addEventListener('change', (e) => {
                const list = filtered();
                const start = (state.page - 1) * state.perPage;
                list.slice(start, start + state.perPage).forEach((t) => {
                    if (e.target.checked) state.checked.add(t.id); else state.checked.delete(t.id);
                });
                renderTable();
            });

            $('tech-table-body').addEventListener('click', (e) => {
                const check = e.target.closest('.row-check');
                if (check) {
                    const id = parseInt(check.dataset.id, 10);
                    if (check.checked) state.checked.add(id); else state.checked.delete(id);
                    renderTable();
                    return;
                }
                const menuBtn = e.target.closest('.row-menu-btn');
                if (menuBtn) {
                    e.stopPropagation();
                    const id = parseInt(menuBtn.dataset.id, 10);
                    if (state.menuId === id && !$('row-menu').classList.contains('hidden')) closeRowMenu(); else openRowMenu(menuBtn, id);
                    return;
                }
                const row = e.target.closest('.tech-row');
                if (row) { closeRowMenu(); selectTech(parseInt(row.dataset.id, 10), true); }
            });

            $('pagination').addEventListener('click', (e) => {
                const b = e.target.closest('[data-page]');
                if (!b || b.disabled) return;
                state.page = parseInt(b.dataset.page, 10);
                renderTable();
            });

            $('row-menu').addEventListener('click', (e) => {
                const b = e.target.closest('[data-act]');
                if (!b || state.menuId == null) return;
                const id = state.menuId;
                closeRowMenu();
                selectTech(id, b.dataset.act === 'detail');
                if (b.dataset.act === 'edit') openForm('edit');
                if (b.dataset.act === 'assign') openAssign();
                if (b.dataset.act === 'toggle') openConfirm();
            });

            document.addEventListener('click', (e) => {
                if (!e.target.closest('#row-menu') && !e.target.closest('.row-menu-btn')) closeRowMenu();
            });
            window.addEventListener('scroll', closeRowMenu, true);

            // ---------- Event: panel ----------
            $('btn-close-panel').addEventListener('click', hidePanel);
            $('tech-backdrop').addEventListener('click', hidePanel);
            document.querySelectorAll('#panel-tabs .tab-btn').forEach((b) => b.addEventListener('click', () => setTab(b.dataset.tab)));

            $('btn-assign').addEventListener('click', openAssign);
            $('btn-location').addEventListener('click', () => openLocation());
            $('ticket-list').addEventListener('click', (e) => {
                const b = e.target.closest('.ticket-loc');
                if (b) openLocation(b.dataset.ticket);
            });

            $('action-status').addEventListener('change', (e) => {
                const t = current();
                if (!t || t.status === e.target.value) return;
                t.history.unshift({ title: 'Status diubah', desc: t.status + ' → ' + e.target.value, time: 'Baru saja' });
                t.status = e.target.value;
                refreshAll();
                showToast('Status ' + t.name + ' diubah menjadi ' + t.status + ' (simulasi frontend).');
            });

            // ---------- Keyboard & resize ----------
            document.addEventListener('keydown', (e) => {
                if (e.key !== 'Escape') return;
                if (!$('row-menu').classList.contains('hidden')) { closeRowMenu(); return; }
                if (closeTopModal()) return;
                if (panelOpen() && !isDesktop()) hidePanel();
            });

            window.addEventListener('resize', () => {
                if (isDesktop()) {
                    $('tech-backdrop').classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                } else if (panelOpen()) {
                    $('tech-backdrop').classList.remove('hidden');
                }
                closeRowMenu();
            });

            // ---------- Inisialisasi ----------
            setTab('detail');
            refreshAll();
            if (isDesktop() && state.data.length) showPanel(); // Desktop: teknisi pertama terpilih. Tablet/mobile: panel menjadi drawer saat baris diklik.
        });
    </script>
</x-app-layout>