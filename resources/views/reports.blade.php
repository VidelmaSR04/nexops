<x-app-layout>
    <div class="space-y-6">

        {{-- 1. HEADER --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-dark tracking-tight">Laporan</h1>
                <p class="text-secondary text-sm mt-0.5">Lihat laporan dan analisis data operasional layanan TIK.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <div id="custom-range" class="hidden items-center gap-1.5 text-xs">
                    <input type="date" id="range-from" aria-label="Tanggal mulai" class="rounded-xl border border-border bg-white px-2.5 py-2 text-dark focus:outline-none focus:ring-2 focus:ring-primary/20">
                    <span class="text-secondary">–</span>
                    <input type="date" id="range-to" aria-label="Tanggal akhir" class="rounded-xl border border-border bg-white px-2.5 py-2 text-dark focus:outline-none focus:ring-2 focus:ring-primary/20">
                </div>
                <div class="relative">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-secondary absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    <select id="report-period-select" aria-label="Periode laporan"
                            class="text-xs rounded-xl border border-border bg-white pl-8 pr-3 py-2 text-dark focus:outline-none focus:ring-2 focus:ring-primary/20">
                        <option value="7">7 Hari Terakhir</option>
                        <option value="30" selected>30 Hari Terakhir</option>
                        <option value="month">Bulan Ini</option>
                        <option value="prev_month">Bulan Sebelumnya</option>
                        <option value="custom">Rentang Tanggal Khusus</option>
                    </select>
                </div>
                <button type="button" id="btn-download-report"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white text-xs font-medium hover:bg-blue-700 shadow-sm transition-colors">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span>Download Laporan</span>
                </button>
            </div>
        </div>

        {{-- 2. KARTU STATISTIK --}}
        <div id="stat-cards" class="grid grid-cols-2 lg:grid-cols-4 gap-4"></div>

        {{-- 3. BARIS 1: TREN + LOKASI OPD --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
            <section class="lg:col-span-3 bg-white rounded-2xl border border-border shadow-[0_2px_6px_rgba(0,0,0,0.02)] p-5 relative">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-bold text-dark">Tren Tiket</h2>
                        <p class="text-xs text-secondary mt-0.5">Perkembangan tiket masuk dan selesai dalam periode terpilih.</p>
                    </div>
                    <div class="flex items-center gap-4 text-[11px] text-secondary">
                        <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full" style="background:#2563EB"></span>Tiket Masuk</span>
                        <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full" style="background:#10B981"></span>Tiket Selesai</span>
                    </div>
                </div>
                <div id="chart-trend" class="mt-3 min-h-[240px]"></div>
            </section>

            <section class="lg:col-span-2 bg-white rounded-2xl border border-border shadow-[0_2px_6px_rgba(0,0,0,0.02)] p-5 relative">
                <h2 class="text-sm font-bold text-dark">Tiket per Lokasi OPD</h2>
                <p class="text-xs text-secondary mt-0.5">Distribusi tiket berdasarkan lokasi OPD.</p>
                <div id="chart-opd" class="mt-4 min-h-[200px]"></div>
            </section>
        </div>

        {{-- 4. BARIS 2: KATEGORI + TEKNISI + STATUS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <section class="bg-white rounded-2xl border border-border shadow-[0_2px_6px_rgba(0,0,0,0.02)] p-5 relative">
                <h2 class="text-sm font-bold text-dark">Tiket per Kategori</h2>
                <p class="text-xs text-secondary mt-0.5">Kategori masalah yang paling sering terjadi.</p>
                <div id="chart-category" class="mt-4 min-h-[220px]"></div>
            </section>

            <section class="bg-white rounded-2xl border border-border shadow-[0_2px_6px_rgba(0,0,0,0.02)] p-5">
                <h2 class="text-sm font-bold text-dark">Performa Teknisi</h2>
                <p class="text-xs text-secondary mt-0.5">Top 5 teknisi dengan penyelesaian tiket terbanyak.</p>
                <div id="tech-ranking" class="mt-3"></div>
            </section>

            <section class="md:col-span-2 xl:col-span-1 bg-white rounded-2xl border border-border shadow-[0_2px_6px_rgba(0,0,0,0.02)] p-5 relative">
                <h2 class="text-sm font-bold text-dark">Status Tiket</h2>
                <p class="text-xs text-secondary mt-0.5">Persentase status tiket dalam periode terpilih.</p>
                <div id="chart-status" class="mt-4 min-h-[200px]"></div>
            </section>
        </div>
    </div>

    <div id="chart-tooltip" class="hidden fixed z-50 pointer-events-none rounded-lg bg-dark text-white text-[11px] px-2.5 py-1.5 shadow-lg"></div>
    <div id="toast-notification" class="fixed bottom-5 right-5 z-50 hidden max-w-sm rounded-xl bg-dark text-white text-xs px-4 py-3 shadow-xl transition-all"></div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // ======================================================
        // Data
        // ======================================================
        const MON = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const C = { blue: '#2563EB', green: '#10B981', orange: '#F59E0B', red: '#EF4444', grid: '#E5E7EB', axis: '#6B7280' };
        const PALETTE = ['#2563EB', '#10B981', '#F59E0B', '#8B5CF6', '#06B6D4', '#94A3B8'];

        const $ = (id) => document.getElementById(id);
        const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        const pad2 = (n) => String(n).padStart(2, '0');
        const ymd = (d) => d.getFullYear() + '-' + pad2(d.getMonth() + 1) + '-' + pad2(d.getDate());
        const parseYmd = (s) => { const [y, m, d] = s.split('-').map(Number); return new Date(y, m - 1, d); };
        const addDays = (d, n) => { const x = new Date(d); x.setDate(x.getDate() + n); return x; };
        const diffDays = (a, b) => Math.round((b - a) / 86400000);
        const fmtShort = (d) => d.getDate() + ' ' + MON[d.getMonth()];
        const fmtLong = (d) => d.getDate() + ' ' + MON[d.getMonth()] + ' ' + d.getFullYear();
        const pctText = (v) => v.toFixed(1).replace('.', ',') + '%';
        const initials = (n) => String(n).trim().split(/\s+/).map((w) => w[0]).slice(0, 2).join('').toUpperCase();

        let today = new Date(); today.setHours(0, 0, 0, 0);
        let todayStr = ymd(today);

        let TICKETS = @json($reportTickets);
        const reportDataUrl = @json($reportDataUrl);

        // ======================================================
        // Periode
        // ======================================================
        function getPeriod() {
            const v = $('report-period-select').value;
            let start, end = today;
            if (v === '7') start = addDays(today, -6);
            else if (v === '30') start = addDays(today, -29);
            else if (v === 'month') start = new Date(today.getFullYear(), today.getMonth(), 1);
            else if (v === 'prev_month') { start = new Date(today.getFullYear(), today.getMonth() - 1, 1); end = new Date(today.getFullYear(), today.getMonth(), 0); }
            else {
                start = $('range-from').value ? parseYmd($('range-from').value) : addDays(today, -29);
                end = $('range-to').value ? parseYmd($('range-to').value) : today;
            }
            return { start, end };
        }
        const inRange = (s, a, b) => !!s && s >= ymd(a) && s <= ymd(b);

        // Klasifikasi saling eksklusif: selesai / pending (belum lewat SLA) / overdue (belum selesai dan lewat SLA)
        const classify = (t) => t.resolved_at ? 'selesai' : ((t.due_at && t.due_at < todayStr) ? 'overdue' : 'pending');
        function summarize(list) {
            const s = { total: list.length, selesai: 0, pending: 0, overdue: 0 };
            list.forEach((t) => { s[classify(t)]++; });
            return s;
        }

        // ======================================================
        // Util tampilan
        // ======================================================
        let toastTimer;
        function showToast(m) { const t = $('toast-notification'); t.textContent = m; t.classList.remove('hidden'); clearTimeout(toastTimer); toastTimer = setTimeout(() => t.classList.add('hidden'), 2800); }

        const tip = $('chart-tooltip');
        function showTip(e, html) {
            tip.innerHTML = html; tip.classList.remove('hidden');
            const pad = 12, w = tip.offsetWidth, h = tip.offsetHeight;
            let x = e.clientX + pad, y = e.clientY + pad;
            if (x + w > window.innerWidth - 8) x = e.clientX - w - pad;
            if (y + h > window.innerHeight - 8) y = e.clientY - h - pad;
            tip.style.left = Math.max(8, x) + 'px'; tip.style.top = Math.max(8, y) + 'px';
        }
        const hideTip = () => tip.classList.add('hidden');
        const emptyState = (msg) => '<div class="h-full min-h-[180px] flex flex-col items-center justify-center text-center text-gray-400 text-xs"><i data-lucide="bar-chart-2" class="w-8 h-8 mb-2 text-gray-300"></i>' + esc(msg || 'Tidak ada data pada periode ini.') + '</div>';
        const refreshIcons = () => { if (window.lucide) window.lucide.createIcons(); };

        function niceTicks(max) {
            if (max <= 0) return { top: 4, step: 1 };
            const raw = max / 4, mag = Math.pow(10, Math.floor(Math.log10(raw))), n = raw / mag;
            const step = (n <= 1 ? 1 : n <= 2 ? 2 : n <= 5 ? 5 : 10) * mag;
            return { top: Math.ceil(max / step) * step, step };
        }

        // Pasang tooltip pada elemen yang membawa data-tip
        function bindTips(root) {
            root.querySelectorAll('[data-tip]').forEach((el) => {
                el.addEventListener('mousemove', (e) => showTip(e, el.getAttribute('data-tip')));
                el.addEventListener('mouseleave', hideTip);
            });
        }

        // ======================================================
        // Kartu statistik
        // ======================================================
        function renderCards(cur, prev) {
            const defs = [
                { key: 'total', label: 'Total Tiket', sub: 'Seluruh tiket pada periode', icon: 'ticket', color: 'bg-blue-50 text-blue-600', good: null },
                { key: 'selesai', label: 'Tiket Selesai', sub: 'Sudah selesai ditangani', icon: 'check-circle-2', color: 'bg-emerald-50 text-emerald-600', good: true },
                { key: 'pending', label: 'Tiket Pending', sub: 'Belum selesai, masih dalam batas SLA', icon: 'clock', color: 'bg-amber-50 text-amber-600', good: false },
                { key: 'overdue', label: 'Tiket Overdue', sub: 'Belum selesai dan melewati SLA', icon: 'alert-triangle', color: 'bg-red-50 text-red-600', good: false },
            ];
            $('stat-cards').innerHTML = defs.map((d) => {
                const p = prev[d.key], v = cur[d.key];
                let delta = '<span class="text-gray-400">Belum ada data pembanding</span>';
                if (p > 0) {
                    const ch = ((v - p) / p) * 100;
                    const up = ch > 0, same = ch === 0;
                    const tone = same || d.good === null ? 'text-secondary' : ((up === d.good) ? 'text-emerald-600' : 'text-red-600');
                    delta = '<span class="font-semibold ' + tone + '">' + (same ? '' : (up ? '▲ ' : '▼ ')) + pctText(Math.abs(ch)) + '</span> <span class="text-gray-400">dari periode sebelumnya</span>';
                }
                return '<div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">'
                    + '<div class="w-11 h-11 rounded-xl ' + d.color + ' flex items-center justify-center shrink-0"><i data-lucide="' + d.icon + '" class="w-5 h-5"></i></div>'
                    + '<div class="min-w-0"><p class="text-xs text-secondary font-medium">' + d.label + '</p>'
                    + '<p class="text-2xl font-bold text-dark mt-0.5">' + v.toLocaleString('id-ID') + '</p>'
                    + '<p class="text-[11px] mt-0.5">' + delta + '</p></div></div>';
            }).join('');
        }

        // ======================================================
        // Line chart: Tren Tiket
        // ======================================================
        function buildBuckets(start, end) {
            const days = diffDays(start, end) + 1, out = [];
            if (days <= 62) {
                for (let i = 0; i < days; i++) { const d = addDays(start, i); out.push({ from: ymd(d), to: ymd(d), label: fmtShort(d), title: fmtLong(d) }); }
            } else if (days <= 200) {
                for (let s = new Date(start); s <= end; s = addDays(s, 7)) {
                    const e = addDays(s, 6) > end ? end : addDays(s, 6);
                    out.push({ from: ymd(s), to: ymd(e), label: fmtShort(s), title: fmtShort(s) + ' – ' + fmtLong(e) });
                }
            } else {
                for (let s = new Date(start.getFullYear(), start.getMonth(), 1); s <= end; s = new Date(s.getFullYear(), s.getMonth() + 1, 1)) {
                    const from = s < start ? start : s, last = new Date(s.getFullYear(), s.getMonth() + 1, 0), to = last > end ? end : last;
                    out.push({ from: ymd(from), to: ymd(to), label: MON[s.getMonth()] + ' ' + String(s.getFullYear()).slice(2), title: MON[s.getMonth()] + ' ' + s.getFullYear() });
                }
            }
            return out;
        }

        function renderTrend(start, end) {
            const box = $('chart-trend');
            const inPeriodAny = TICKETS.some((t) => inRange(t.created_at, start, end) || inRange(t.resolved_at, start, end));
            if (!inPeriodAny) { box.innerHTML = emptyState(); refreshIcons(); return; }

            const bk = buildBuckets(start, end).map((b) => ({
                ...b,
                masuk: TICKETS.filter((t) => t.created_at >= b.from && t.created_at <= b.to).length,
                selesai: TICKETS.filter((t) => t.resolved_at && t.resolved_at >= b.from && t.resolved_at <= b.to).length,
            }));
            const W = 640, H = 250, L = 34, R = 12, T = 12, B = 30, pw = W - L - R, ph = H - T - B;
            const max = Math.max(...bk.map((b) => Math.max(b.masuk, b.selesai)));
            const { top, step } = niceTicks(max);
            const n = bk.length;
            const X = (i) => n === 1 ? L + pw / 2 : L + (i * pw) / (n - 1);
            const Y = (v) => T + ph - (v / top) * ph;

            let g = '';
            for (let v = 0; v <= top; v += step) {
                g += '<line x1="' + L + '" x2="' + (W - R) + '" y1="' + Y(v) + '" y2="' + Y(v) + '" stroke="' + C.grid + '" stroke-width="1"/>'
                   + '<text x="' + (L - 6) + '" y="' + (Y(v) + 3) + '" text-anchor="end" font-size="10" fill="' + C.axis + '">' + v + '</text>';
            }
            const every = Math.ceil(n / 8);
            bk.forEach((b, i) => { if (i % every === 0 || i === n - 1) g += '<text x="' + X(i) + '" y="' + (H - 8) + '" text-anchor="middle" font-size="10" fill="' + C.axis + '">' + esc(b.label) + '</text>'; });

            const path = (k) => bk.map((b, i) => (i ? 'L' : 'M') + X(i).toFixed(1) + ' ' + Y(b[k]).toFixed(1)).join(' ');
            const area = (k) => path(k) + ' L' + X(n - 1).toFixed(1) + ' ' + Y(0) + ' L' + X(0).toFixed(1) + ' ' + Y(0) + ' Z';
            const r = n > 40 ? 2 : 3;
            const dots = (k, col) => bk.map((b, i) => '<circle cx="' + X(i).toFixed(1) + '" cy="' + Y(b[k]).toFixed(1) + '" r="' + r + '" fill="#fff" stroke="' + col + '" stroke-width="1.5"/>').join('');
            const hw = n === 1 ? pw : pw / (n - 1);
            const hits = bk.map((b, i) => '<rect class="hit" x="' + (X(i) - hw / 2).toFixed(1) + '" y="' + T + '" width="' + hw.toFixed(1) + '" height="' + ph + '" fill="transparent" data-i="' + i
                + '" data-tip="<b>' + esc(b.title) + '</b><br>Tiket Masuk: ' + b.masuk + '<br>Tiket Selesai: ' + b.selesai + '"/>').join('');

            box.innerHTML = '<svg viewBox="0 0 ' + W + ' ' + H + '" class="w-full h-auto" role="img" aria-label="Grafik tren tiket masuk dan selesai">'
                + '<defs><linearGradient id="gBlue" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="' + C.blue + '" stop-opacity=".12"/><stop offset="1" stop-color="' + C.blue + '" stop-opacity="0"/></linearGradient>'
                + '<linearGradient id="gGreen" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="' + C.green + '" stop-opacity=".12"/><stop offset="1" stop-color="' + C.green + '" stop-opacity="0"/></linearGradient></defs>'
                + g
                + '<path d="' + area('masuk') + '" fill="url(#gBlue)"/><path d="' + area('selesai') + '" fill="url(#gGreen)"/>'
                + '<path d="' + path('masuk') + '" fill="none" stroke="' + C.blue + '" stroke-width="2" stroke-linejoin="round"/>'
                + '<path d="' + path('selesai') + '" fill="none" stroke="' + C.green + '" stroke-width="2" stroke-linejoin="round"/>'
                + dots('masuk', C.blue) + dots('selesai', C.green) + hits + '</svg>';
            bindTips(box);
        }

        // ======================================================
        // Donut chart (dipakai Lokasi OPD dan Status Tiket)
        // ======================================================
        function renderDonut(box, segs, centerLabel) {
            const total = segs.reduce((a, s) => a + s.value, 0);
            if (!total) { box.innerHTML = emptyState(); refreshIcons(); return; }
            let cum = 0;
            const rings = segs.filter((s) => s.value > 0).map((s) => {
                const pct = (s.value / total) * 100;
                const el = '<circle cx="21" cy="21" r="15.9155" fill="none" stroke="' + s.color + '" stroke-width="5" stroke-dasharray="' + pct.toFixed(3) + ' ' + (100 - pct).toFixed(3)
                    + '" stroke-dashoffset="' + (-cum).toFixed(3) + '" transform="rotate(-90 21 21)" class="cursor-pointer" data-tip="<b>' + esc(s.label) + '</b><br>' + s.value.toLocaleString('id-ID') + ' tiket (' + pctText(pct) + ')"/>';
                cum += pct; return el;
            }).join('');
            const legend = segs.map((s) => '<li class="flex items-center gap-2 py-1.5"><span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:' + s.color + '"></span>'
                + '<span class="flex-1 min-w-0 truncate text-dark" title="' + esc(s.label) + '">' + esc(s.label) + '</span>'
                + '<span class="text-secondary w-12 text-right">' + pctText((s.value / total) * 100) + '</span>'
                + '<span class="font-semibold text-dark w-9 text-right">' + s.value.toLocaleString('id-ID') + '</span></li>').join('');
            box.innerHTML = '<div class="flex flex-col sm:flex-row items-center gap-5">'
                + '<div class="relative w-40 h-40 shrink-0"><svg viewBox="0 0 42 42" class="w-full h-full" role="img" aria-label="' + esc(centerLabel) + '"><circle cx="21" cy="21" r="15.9155" fill="none" stroke="#F1F5F9" stroke-width="5"/>' + rings + '</svg>'
                + '<div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none"><span class="text-2xl font-bold text-dark leading-none">' + total.toLocaleString('id-ID') + '</span><span class="text-[11px] text-secondary mt-1">' + esc(centerLabel) + '</span></div></div>'
                + '<ul class="flex-1 min-w-0 w-full text-xs divide-y divide-gray-100">' + legend + '</ul></div>';
            bindTips(box);
        }

        function topGroups(list, keyFn, max) {
            const m = new Map();
            list.forEach((t) => { const k = keyFn(t) || 'Tidak diketahui'; m.set(k, (m.get(k) || 0) + 1); });
            let arr = [...m].sort((a, b) => b[1] - a[1]);
            if (arr.length > max) {
                const rest = arr.slice(max - 1);
                const restSum = rest.reduce((a, r) => a + r[1], 0);
                arr = arr.slice(0, max - 1);
                const ex = arr.find((r) => r[0] === 'Lainnya');
                if (ex) ex[1] += restSum; else arr.push(['Lainnya', restSum]);
            }
            return arr;
        }

        function renderOpd(list) {
            const g = topGroups(list, (t) => t.opd, 6);
            renderDonut($('chart-opd'), g.map(([label, value], i) => ({ label, value, color: PALETTE[i % PALETTE.length] })), 'Total Tiket');
        }

        function renderStatus(cur) {
            renderDonut($('chart-status'), [
                { label: 'Selesai', value: cur.selesai, color: C.green },
                { label: 'Pending', value: cur.pending, color: C.orange },
                { label: 'Overdue', value: cur.overdue, color: C.red },
            ], 'Total Tiket');
        }

        // ======================================================
        // Bar chart: Tiket per Kategori
        // ======================================================
        function renderCategory(list) {
            const box = $('chart-category');
            if (!list.length) { box.innerHTML = emptyState(); refreshIcons(); return; }
            const g = topGroups(list, (t) => t.category, 5);
            const total = list.length;
            const W = 420, H = 250, L = 30, R = 8, T = 22, B = 34, pw = W - L - R, ph = H - T - B;
            const { top, step } = niceTicks(Math.max(...g.map((r) => r[1])));
            const Y = (v) => T + ph - (v / top) * ph;
            let s = '';
            for (let v = 0; v <= top; v += step) {
                s += '<line x1="' + L + '" x2="' + (W - R) + '" y1="' + Y(v) + '" y2="' + Y(v) + '" stroke="' + C.grid + '"/>'
                   + '<text x="' + (L - 5) + '" y="' + (Y(v) + 3) + '" text-anchor="end" font-size="10" fill="' + C.axis + '">' + v + '</text>';
            }
            const slot = pw / g.length, bw = Math.min(40, slot * 0.55);
            g.forEach(([label, v], i) => {
                const x = L + slot * i + (slot - bw) / 2, y = Y(v), h = T + ph - y, r = Math.min(6, bw / 2, h);
                const name = label.length > 11 ? label.slice(0, 10) + '…' : label;
                const tipHtml = '<b>' + esc(label) + '</b><br>' + v.toLocaleString('id-ID') + ' tiket (' + pctText((v / total) * 100) + ')';
                if (h > 0) s += '<path d="M' + x + ',' + (T + ph) + ' V' + (y + r) + ' Q' + x + ',' + y + ' ' + (x + r) + ',' + y + ' H' + (x + bw - r) + ' Q' + (x + bw) + ',' + y + ' ' + (x + bw) + ',' + (y + r) + ' V' + (T + ph) + ' Z" fill="' + C.blue + '" class="cursor-pointer" data-tip="' + tipHtml + '"/>';
                s += '<text x="' + (x + bw / 2) + '" y="' + (y - 6) + '" text-anchor="middle" font-size="11" font-weight="600" fill="#0F172A">' + v + '</text>'
                   + '<text x="' + (x + bw / 2) + '" y="' + (H - 12) + '" text-anchor="middle" font-size="10" fill="' + C.axis + '">' + esc(name) + '</text>';
            });
            box.innerHTML = '<svg viewBox="0 0 ' + W + ' ' + H + '" class="w-full h-auto" role="img" aria-label="Grafik jumlah tiket per kategori">' + s + '</svg>';
            bindTips(box);
        }

        // ======================================================
        // Tabel: Performa Teknisi
        // ======================================================
        function renderTechs(start, end) {
            const box = $('tech-ranking');
            const m = new Map();
            TICKETS.filter((t) => inRange(t.resolved_at, start, end) && t.technician_code).forEach((t) => {
                const r = m.get(t.technician_code) || { code: t.technician_code, name: t.technician_name || t.technician_code, count: 0, rs: 0, rn: 0 };
                r.count++;
                if (t.rating != null) { r.rs += Number(t.rating); r.rn++; }
                m.set(t.technician_code, r);
            });
            const rows = [...m.values()].sort((a, b) => b.count - a.count).slice(0, 5);
            if (!rows.length) { box.innerHTML = emptyState('Belum ada tiket selesai pada periode ini.'); refreshIcons(); return; }
            const colors = ['bg-blue-100 text-blue-700', 'bg-emerald-100 text-emerald-700', 'bg-amber-100 text-amber-700', 'bg-rose-100 text-rose-700', 'bg-violet-100 text-violet-700'];
            box.innerHTML = '<table class="w-full text-xs"><thead><tr class="text-secondary text-[11px] border-b border-border"><th class="py-2 text-left font-medium w-8">No.</th><th class="py-2 text-left font-medium">Nama Teknisi</th><th class="py-2 text-right font-medium">Jumlah Tiket</th><th class="py-2 text-right font-medium w-16">Rating</th></tr></thead><tbody class="divide-y divide-gray-100">'
                + rows.map((r, i) => {
                    const rating = r.rn ? '<span class="inline-flex items-center gap-1 font-medium text-dark"><i data-lucide="star" class="w-3.5 h-3.5 text-amber-400 fill-amber-400"></i>' + (r.rs / r.rn).toFixed(1) + '</span>' : '<span class="text-gray-400">–</span>';
                    return '<tr><td class="py-2.5 text-secondary">' + (i + 1) + '</td>'
                        + '<td class="py-2.5"><div class="flex items-center gap-2.5 min-w-0"><span class="w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-semibold shrink-0 ' + colors[i % colors.length] + '">' + esc(initials(r.name)) + '</span>'
                        + '<div class="min-w-0"><p class="font-medium text-dark truncate">' + esc(r.name) + '</p><p class="text-[11px] text-secondary">' + esc(r.code) + '</p></div></div></td>'
                        + '<td class="py-2.5 text-right font-semibold text-dark">' + r.count + '</td><td class="py-2.5 text-right">' + rating + '</td></tr>';
                }).join('') + '</tbody></table>';
            refreshIcons();
        }

        // ======================================================
        // Render semua (satu sumber data, satu periode)
        // ======================================================
        let current = { start: today, end: today, list: [] };
        function renderAll() {
            const { start, end } = getPeriod();
            if (start > end) { showToast('Tanggal mulai tidak boleh setelah tanggal akhir.'); return; }
            const len = diffDays(start, end) + 1;
            const pEnd = addDays(start, -1), pStart = addDays(pEnd, -(len - 1));
            const list = TICKETS.filter((t) => inRange(t.created_at, start, end));
            const prevList = TICKETS.filter((t) => inRange(t.created_at, pStart, pEnd));
            const cur = summarize(list);
            current = { start, end, list };

            renderCards(cur, summarize(prevList));
            renderTrend(start, end);
            renderOpd(list);
            renderCategory(list);
            renderTechs(start, end);
            renderStatus(cur);
            refreshIcons();
        }

        // ======================================================
        // Event
        // ======================================================
        $('report-period-select').addEventListener('change', (e) => {
            const custom = e.target.value === 'custom';
            $('custom-range').classList.toggle('hidden', !custom);
            $('custom-range').classList.toggle('flex', custom);
            if (custom) {
                $('range-to').value = ymd(today); $('range-from').value = ymd(addDays(today, -29));
            }
            renderAll();
        });
        ['range-from', 'range-to'].forEach((id) => { $(id).max = ymd(today); $(id).addEventListener('change', renderAll); });

        // Download: CSV berisi baris tiket yang sama dengan yang dipakai kartu dan grafik pada periode terpilih.
        $('btn-download-report').addEventListener('click', () => {
            const rows = current.list;
            if (!rows.length) { showToast('Tidak ada data pada periode ini untuk diunduh.'); return; }
            const cell = (v) => '"' + String(v ?? '').replace(/"/g, '""') + '"';
            const head = ['No. Tiket', 'Tanggal Dibuat', 'Tanggal Selesai', 'Batas Waktu (SLA)', 'Kategori', 'OPD', 'Kode Teknisi', 'Teknisi', 'Status'];
            const body = rows.map((t) => [t.id, t.created_at, t.resolved_at || '', t.due_at || '', t.category, t.opd, t.technician_code || '', t.technician_name || '',
                ({ selesai: 'Selesai', pending: 'Pending', overdue: 'Overdue' })[classify(t)]].map(cell).join(','));
            const csv = '\uFEFF' + [head.map(cell).join(','), ...body].join('\r\n');
            const url = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8' }));
            const a = document.createElement('a');
            a.href = url; a.download = 'laporan-tiket-' + ymd(current.start) + '_' + ymd(current.end) + '.csv';
            document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);
            showToast('Laporan diunduh (' + rows.length + ' tiket).');
        });

        let refreshInProgress = false;
        async function refreshReportData() {
            if (refreshInProgress) return;
            refreshInProgress = true;
            try {
                const response = await fetch(reportDataUrl, {
                    headers: { Accept: 'application/json' },
                    cache: 'no-store',
                });
                if (!response.ok) throw new Error('Data laporan gagal diperbarui.');
                const payload = await response.json();
                if (!Array.isArray(payload.data)) throw new Error('Format data laporan tidak valid.');
                TICKETS = payload.data;
                today = new Date(); today.setHours(0, 0, 0, 0);
                todayStr = ymd(today);
                renderAll();
            } catch (error) {
                showToast(error.message || 'Data laporan gagal diperbarui.');
            } finally {
                refreshInProgress = false;
            }
        }

        renderAll();
        window.setInterval(refreshReportData, 60000);
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') refreshReportData();
        });
    });
    </script>
</x-app-layout>
