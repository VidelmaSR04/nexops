<x-app-layout>
    <div class="space-y-6">

        {{-- 1. HEADER CUSTOMERS --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-dark tracking-tight">Customers</h1>
                <p class="text-secondary text-sm mt-0.5">Kelola data pelanggan dan informasi layanan</p>
            </div>
            <div>
                <button type="button" id="btn-open-add-modal"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-white text-sm font-medium hover:bg-blue-700 shadow-sm transition-colors cursor-pointer">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    <span>Tambah Customer</span>
                </button>
            </div>
        </div>

        {{-- 2. KARTU STATISTIK (4 Buah) --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Total Customer</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">1,248</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i data-lucide="user-check" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Pelanggan Aktif</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">1,102</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <i data-lucide="user-plus" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Pelanggan Baru</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">86</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                    <i data-lucide="user-minus" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Pelanggan Nonaktif</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">60</p>
                </div>
            </div>
        </div>

        {{-- 3. LAYOUT UTAMA: TABEL & PANEL DETAIL (2 KOLOM) --}}
        <div class="flex flex-col xl:flex-row items-start gap-5">

            {{-- KOLOM KIRI: TABEL PELANGGAN --}}
            <div class="w-full xl:flex-1 bg-white rounded-2xl border border-border shadow-[0_2px_6px_rgba(0,0,0,0.02)] overflow-hidden">

                {{-- Search & Filter Toolbar --}}
                <div class="p-4 border-b border-border space-y-3">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="relative flex-1 min-w-[220px]">
                            <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                            <input id="searchInput" type="text"
                                   placeholder="Cari nama, NIK, nomor pelanggan, atau alamat..."
                                   class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-border bg-surface focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        </div>

                        <select id="statusFilter" class="text-xs rounded-xl border border-border bg-white px-3 py-2 text-dark focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <option value="">Semua Status</option>
                            <option value="Aktif">Aktif</option>
                            <option value="Baru">Baru</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>

                        <select id="areaFilter" class="text-xs rounded-xl border border-border bg-white px-3 py-2 text-dark focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <option value="">Semua Area</option>
                            <option value="Situbondo">Situbondo</option>
                            <option value="Besuki">Besuki</option>
                            <option value="Lapangan">Lapangan</option>
                            <option value="Banyuglugur">Banyuglugur</option>
                        </select>

                        <button type="button" id="resetFilterBtn" class="px-3 py-2 rounded-xl border border-border text-xs font-medium text-secondary hover:bg-gray-50 transition-colors">
                            Reset
                        </button>
                    </div>
                </div>

                {{-- Tabel Data --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr class="text-secondary bg-[#FAFCFF] border-b border-border font-medium text-[11px] uppercase tracking-wider">
                                <th class="pl-4 py-3 w-8">
                                    <input id="selectAll" type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary/20">
                                </th>
                                <th class="py-3 pr-3">Customer</th>
                                <th class="py-3 pr-3">NIK</th>
                                <th class="py-3 pr-3">No. Pelanggan</th>
                                <th class="py-3 pr-3">Area</th>
                                <th class="py-3 pr-3">Status</th>
                                <th class="py-3 pr-3">Paket</th>
                                <th class="py-3 pr-4 text-center w-8">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="customerTableBody" class="divide-y divide-gray-100">
                            {{-- Rendered via JavaScript --}}
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="p-3.5 border-t border-border flex flex-wrap items-center justify-between gap-3 text-xs">
                    <p class="text-secondary">Menampilkan <span id="totalVisibleCount">10</span> dari 1,248 total data</p>
                    <div class="flex items-center gap-1">
                        <button type="button" class="w-7 h-7 rounded-lg border border-border flex items-center justify-center text-secondary hover:bg-gray-50">
                            <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
                        </button>
                        <button type="button" class="w-7 h-7 rounded-lg bg-primary text-white font-medium flex items-center justify-center">1</button>
                        <button type="button" class="w-7 h-7 rounded-lg border border-border text-secondary hover:bg-gray-50 flex items-center justify-center">2</button>
                        <button type="button" class="w-7 h-7 rounded-lg border border-border text-secondary hover:bg-gray-50 flex items-center justify-center">3</button>
                        <button type="button" class="w-7 h-7 rounded-lg border border-border flex items-center justify-center text-secondary hover:bg-gray-50">
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>

            </div>

            {{-- KOLOM KANAN: PANEL DETAIL PELANGGAN --}}
            <aside class="w-full xl:w-[480px] 2xl:w-[520px] bg-white rounded-2xl border border-border shadow-[0_2px_6px_rgba(0,0,0,0.02)] p-5 shrink-0 xl:sticky xl:top-20">

                <div class="flex items-center justify-between pb-4 border-b border-border mb-4">
                    <h3 class="text-base font-bold text-dark">Panel Detail Pelanggan</h3>
                    <button type="button" onclick="resetDetailPanel()" class="p-1 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                {{-- Profil Ringkas --}}
                <div class="flex items-center gap-3.5 p-3.5 rounded-xl bg-surface border border-border/80 mb-4">
                    <img id="detailAvatar" src="https://ui-avatars.com/api/?name=Budi+Santoso&background=0D6EFD&color=fff" alt="Avatar" class="w-12 h-12 rounded-full shrink-0">
                    <div class="flex-1 min-w-0">
                        <h4 id="detailName" class="font-bold text-dark text-sm truncate">Budi Santoso</h4>
                        <span id="detailCustNo" class="inline-block px-2 py-0.5 mt-0.5 rounded text-[11px] font-semibold bg-blue-50 text-primary border border-blue-200">CST0003</span>
                    </div>
                    <div id="detailStatusBadge">
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-600">Aktif</span>
                    </div>
                </div>

                {{-- Status & Aksi Cepat --}}
                <div class="p-3.5 rounded-xl border border-border bg-surface space-y-3 mb-4 text-xs">
                    <p class="font-semibold text-dark">Panel Status & Aksi Cepat</p>
                    <div class="grid grid-cols-2 gap-2">
                        <select id="quickStatusSelect" class="rounded-lg border border-border bg-white px-2.5 py-2 text-dark focus:outline-none focus:ring-2 focus:ring-primary/20" onchange="updateCustomerStatusFromPanel(this.value)">
                            <option value="Aktif">Status: Aktif</option>
                            <option value="Baru">Status: Baru</option>
                            <option value="Nonaktif">Status: Nonaktif</option>
                        </select>
                        <button type="button" onclick="openAssignTicketModal()" class="py-2 px-3 rounded-lg bg-primary text-white font-medium hover:bg-blue-700 transition-colors text-center">
                            Assign Ticket
                        </button>
                    </div>
                    <div class="grid grid-cols-3 gap-2 pt-1">
                        <button type="button" onclick="switchTab('riwayat')" class="py-2 px-2 rounded-lg bg-white border border-border text-secondary hover:text-dark hover:bg-gray-50 text-center font-medium">Riwayat</button>
                        <button type="button" onclick="switchTab('tagihan')" class="py-2 px-2 rounded-lg bg-white border border-border text-secondary hover:text-dark hover:bg-gray-50 text-center font-medium">Tagihan</button>
                        <button type="button" onclick="openDeactivateModal()" class="py-2 px-2 rounded-lg bg-red-50 border border-red-200 text-danger hover:bg-red-100 text-center font-medium">Nonaktifkan</button>
                    </div>
                </div>

                {{-- Quick Info Card --}}
                <div class="p-3.5 rounded-xl bg-gradient-to-br from-primary to-blue-700 text-white space-y-2 mb-4 text-xs shadow-sm">
                    <div class="flex items-center justify-between opacity-80 text-[11px] uppercase font-semibold">
                        <span>Quick Info Layanan</span>
                        <i data-lucide="info" class="w-4 h-4"></i>
                    </div>
                    <div class="grid grid-cols-3 gap-2 pt-1">
                        <div>
                            <span class="opacity-75 text-[10px] block">Area</span>
                            <strong id="qiArea" class="text-sm">Situbondo</strong>
                        </div>
                        <div>
                            <span class="opacity-75 text-[10px] block">Paket</span>
                            <strong id="qiPaket" class="text-sm">50 Mbps</strong>
                        </div>
                        <div>
                            <span class="opacity-75 text-[10px] block">Tagihan Terakhir</span>
                            <strong id="qiTagihan" class="text-sm">Rp350.000</strong>
                        </div>
                    </div>
                </div>

                {{-- Tab Navigation --}}
                <div class="flex border-b border-border text-xs font-semibold mb-4 gap-6" id="customerTabs">
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-primary text-primary transition-colors" onclick="switchTab('detail')">Detail</button>
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-transparent text-secondary hover:text-dark transition-colors" onclick="switchTab('riwayat')">Riwayat</button>
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-transparent text-secondary hover:text-dark transition-colors" onclick="switchTab('tagihan')">Tagihan</button>
                    <button type="button" class="tab-btn pb-2.5 border-b-2 border-transparent text-secondary hover:text-dark transition-colors" onclick="switchTab('dokumen')">Dokumen</button>
                </div>

                {{-- Tab Contents --}}
                <div class="text-xs space-y-3 max-h-[300px] overflow-y-auto pr-1">
                    {{-- Tab 1: Detail --}}
                    <div id="tab-pane-detail" class="tab-pane space-y-2.5">
                        <div class="flex justify-between py-1 border-b border-gray-100">
                            <span class="text-secondary">NIK</span>
                            <span id="valNik" class="font-medium text-dark">NIK001003</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-100">
                            <span class="text-secondary">Nomor Telepon</span>
                            <span id="valPhone" class="font-medium text-dark">0812 3456 7890</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-100">
                            <span class="text-secondary">Email</span>
                            <span id="valEmail" class="font-medium text-dark">budi.santoso@email.com</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-100">
                            <span class="text-secondary">Alamat</span>
                            <span id="valAddress" class="font-medium text-dark text-right max-w-[60%]">Jl. Sudirman No. 45, Situbondo</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-100">
                            <span class="text-secondary">Tanggal Bergabung</span>
                            <span class="font-medium text-dark">12 Januari 2024</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="text-secondary">Ticket Aktif</span>
                            <span class="px-2 py-0.5 rounded font-semibold bg-amber-50 text-amber-700">2 Ticket</span>
                        </div>
                    </div>

                    {{-- Tab 2: Riwayat --}}
                    <div id="tab-pane-riwayat" class="tab-pane hidden space-y-3">
                        <div class="space-y-3 relative before:absolute before:inset-0 before:left-2 before:w-0.5 before:bg-gray-200">
                            <div class="relative flex items-start gap-3 pl-6">
                                <span class="absolute left-1 top-1 w-2.5 h-2.5 rounded-full bg-primary ring-4 ring-white"></span>
                                <div>
                                    <p class="font-semibold text-dark">Customer terdaftar</p>
                                    <p class="text-[11px] text-secondary">Pendaftaran akun dan aktivasi awal • 12 Jan 2024</p>
                                </div>
                            </div>
                            <div class="relative flex items-start gap-3 pl-6">
                                <span class="absolute left-1 top-1 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-white"></span>
                                <div>
                                    <p class="font-semibold text-dark">Paket layanan diperbarui</p>
                                    <p class="text-[11px] text-secondary">Upgrade ke 50 Mbps • 15 Jun 2024</p>
                                </div>
                            </div>
                            <div class="relative flex items-start gap-3 pl-6">
                                <span class="absolute left-1 top-1 w-2.5 h-2.5 rounded-full bg-amber-500 ring-4 ring-white"></span>
                                <div>
                                    <p class="font-semibold text-dark">Ticket gangguan dibuat</p>
                                    <p class="text-[11px] text-secondary">Tiket #TCK-8829 koneksi lambat • 10 Okt 2025</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tab 3: Tagihan --}}
                    <div id="tab-pane-tagihan" class="tab-pane hidden space-y-2">
                        <table class="w-full text-center border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-secondary text-[11px] border-b border-border">
                                    <th class="p-2">ID</th>
                                    <th class="p-2">Periode</th>
                                    <th class="p-2">Nominal</th>
                                    <th class="p-2">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr>
                                    <td class="p-2 font-medium">INV-03</td>
                                    <td class="p-2">Maret 2026</td>
                                    <td class="p-2">Rp350.000</td>
                                    <td class="p-2"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-600">Lunas</span></td>
                                </tr>
                                <tr>
                                    <td class="p-2 font-medium">INV-02</td>
                                    <td class="p-2">Feb 2026</td>
                                    <td class="p-2">Rp350.000</td>
                                    <td class="p-2"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-600">Lunas</span></td>
                                </tr>
                                <tr>
                                    <td class="p-2 font-medium">INV-01</td>
                                    <td class="p-2">Jan 2026</td>
                                    <td class="p-2">Rp350.000</td>
                                    <td class="p-2"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-red-50 text-red-600">Terlambat</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Tab 4: Dokumen --}}
                    <div id="tab-pane-dokumen" class="tab-pane hidden space-y-2">
                        <div class="flex items-center justify-between p-2.5 rounded-xl border border-border bg-surface">
                            <div class="flex items-center gap-2">
                                <i data-lucide="file-text" class="w-4 h-4 text-red-500"></i>
                                <span class="font-medium text-dark">KTP_Budi_Santoso.pdf</span>
                            </div>
                            <span class="text-[10px] text-secondary">PDF</span>
                        </div>
                        <div class="flex items-center justify-between p-2.5 rounded-xl border border-border bg-surface">
                            <div class="flex items-center gap-2">
                                <i data-lucide="image" class="w-4 h-4 text-blue-500"></i>
                                <span class="font-medium text-dark">Foto_Lokasi_ONT.jpg</span>
                            </div>
                            <span class="text-[10px] text-secondary">JPG</span>
                        </div>
                    </div>
                </div>

            </aside>

        </div>

    </div>

    {{-- MODALS PROTOTYPE --}}

    {{-- Modal Tambah Customer --}}
    <div id="modal-add-customer" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs hidden">
        <div class="bg-white rounded-2xl border border-border shadow-2xl max-w-lg w-full overflow-hidden">
            <div class="p-4 border-b border-border flex items-center justify-between">
                <h3 class="text-base font-bold text-dark">Tambah Customer Baru</h3>
                <button type="button" onclick="closeAddModal()" class="p-1 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form onsubmit="handleSimulatedSubmit(event, 'add')" class="p-5 space-y-3.5 text-xs">
                <div>
                    <label class="block font-medium text-dark mb-1">Nama Lengkap</label>
                    <input type="text" required placeholder="Contoh: Rian Utama" class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1">NIK</label>
                        <input type="text" required placeholder="NIK001011" class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">No. Pelanggan</label>
                        <input type="text" value="CST0011" readonly class="w-full rounded-xl border border-border bg-gray-50 px-3 py-2 text-xs text-secondary">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1">Nomor Telepon</label>
                        <input type="text" required placeholder="081299887766" class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Area</label>
                        <select class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                            <option value="Situbondo">Situbondo</option>
                            <option value="Besuki">Besuki</option>
                            <option value="Lapangan">Lapangan</option>
                            <option value="Banyuglugur">Banyuglugur</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1">Alamat Lengkap</label>
                    <textarea rows="2" required placeholder="Nama jalan, nomor rumah..." class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1">Paket Layanan</label>
                        <select class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                            <option value="50 Mbps">50 Mbps</option>
                            <option value="100 Mbps">100 Mbps</option>
                            <option value="200 Mbps">200 Mbps</option>
                            <option value="300 Mbps">300 Mbps</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Status</label>
                        <select class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                            <option value="Aktif">Aktif</option>
                            <option value="Baru">Baru</option>
                        </select>
                    </div>
                </div>
                <div class="pt-3 border-t border-border flex items-center justify-end gap-2">
                    <button type="button" onclick="closeAddModal()" class="px-3.5 py-2 rounded-xl border border-border text-secondary hover:bg-gray-50 font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-blue-700 shadow-sm">Simpan Customer</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Assign Ticket --}}
    <div id="modal-assign-ticket" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs hidden">
        <div class="bg-white rounded-2xl border border-border shadow-2xl max-w-md w-full overflow-hidden">
            <div class="p-4 border-b border-border flex items-center justify-between">
                <h3 class="text-base font-bold text-dark">Assign Ticket Gangguan</h3>
                <button type="button" onclick="closeAssignModal()" class="p-1 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form onsubmit="handleTicketSubmit(event)" class="p-5 space-y-3.5 text-xs">
                <div>
                    <label class="block font-medium text-dark mb-1">Pelanggan</label>
                    <input type="text" id="ticketTargetCustomer" readonly class="w-full rounded-xl border border-border bg-gray-50 px-3 py-2 text-xs text-secondary">
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1">Kategori Kendala</label>
                    <select class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                        <option value="Koneksi Putus">Koneksi Putus / Loss Signal</option>
                        <option value="Internet Lambat">Internet Lambat</option>
                        <option value="Perangkat ONT Rusak">Perangkat ONT Rusak</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1">Deskripsi</label>
                    <textarea rows="3" required placeholder="Jelaskan kendala..." class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20"></textarea>
                </div>
                <div class="pt-3 border-t border-border flex items-center justify-end gap-2">
                    <button type="button" onclick="closeAssignModal()" class="px-3.5 py-2 rounded-xl border border-border text-secondary hover:bg-gray-50 font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-blue-700 shadow-sm">Kirim Ticket</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Dialog Konfirmasi Nonaktifkan --}}
    <div id="modal-deactivate" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs hidden">
        <div class="bg-white rounded-2xl border border-border shadow-2xl max-w-sm w-full p-5 text-center space-y-3.5">
            <div class="w-12 h-12 rounded-full bg-red-50 text-red-600 flex items-center justify-center mx-auto">
                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>
            <h3 class="text-base font-bold text-dark">Nonaktifkan Pelanggan?</h3>
            <p class="text-secondary text-xs">Layanan pelanggan terpilih akan dinonaktifkan sementara.</p>
            <div class="flex items-center justify-center gap-2 pt-2">
                <button type="button" onclick="closeDeactivateModal()" class="px-4 py-2 rounded-xl border border-border text-secondary hover:bg-gray-50 font-medium text-xs">Batal</button>
                <button type="button" onclick="confirmDeactivate()" class="px-4 py-2 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700 text-xs shadow-sm">Ya, Nonaktifkan</button>
            </div>
        </div>
    </div>

    {{-- JAVASCRIPT LOGIC --}}
    <script>
        const serverData = @json($customersData ?? null);
        const customersData = serverData || [
            { id: 1, name: "Andi Pratama", nik: "NIK001001", custNo: "CST0001", area: "Situbondo", status: "Aktif", paket: "100 Mbps", phone: "081211112222", email: "andi.pratama@email.com", address: "Jl. Melati No. 10, Situbondo", tagihan: "Rp450.000" },
            { id: 2, name: "Siti Rahmawati", nik: "NIK001002", custNo: "CST0002", area: "Besuki", status: "Aktif", paket: "200 Mbps", phone: "081222223333", email: "siti.rahmawati@email.com", address: "Jl. A. Yani No. 12, Besuki", tagihan: "Rp650.000" },
            { id: 3, name: "Budi Santoso", nik: "NIK001003", custNo: "CST0003", area: "Situbondo", status: "Aktif", paket: "50 Mbps", phone: "0812 3456 7890", email: "budi.santoso@email.com", address: "Jl. Sudirman No. 45, Situbondo", tagihan: "Rp350.000" },
            { id: 4, name: "Dewi Lestari", nik: "NIK001004", custNo: "CST0004", area: "Lapangan", status: "Baru", paket: "100 Mbps", phone: "081233334444", email: "dewi.lestari@email.com", address: "Komplek Lapangan No. 8, Situbondo", tagihan: "Rp450.000" },
            { id: 5, name: "Rizky Maulana", nik: "NIK001005", custNo: "CST0005", area: "Besuki", status: "Aktif", paket: "200 Mbps", phone: "081244445555", email: "rizky.maulana@email.com", address: "Jl. Pantai No. 5, Besuki", tagihan: "Rp650.000" },
            { id: 6, name: "Agus Setiawan", nik: "NIK001006", custNo: "CST0006", area: "Situbondo", status: "Aktif", paket: "50 Mbps", phone: "081255556666", email: "agus.setiawan@email.com", address: "Jl. Diponegoro No. 20, Situbondo", tagihan: "Rp350.000" },
            { id: 7, name: "Rina Amelia", nik: "NIK001007", custNo: "CST0007", area: "Besuki", status: "Nonaktif", paket: "100 Mbps", phone: "081266667777", email: "rina.amelia@email.com", address: "Jl. Mawar No. 3, Besuki", tagihan: "Rp0" },
            { id: 8, name: "Fajar Nugroho", nik: "NIK001008", custNo: "CST0008", area: "Lapangan", status: "Aktif", paket: "300 Mbps", phone: "081277778888", email: "fajar.nugroho@email.com", address: "Jl. Lapangan Timur No. 15, Situbondo", tagihan: "Rp850.000" },
            { id: 9, name: "Maya Sari", nik: "NIK001009", custNo: "CST0009", area: "Situbondo", status: "Aktif", paket: "50 Mbps", phone: "081288889999", email: "maya.sari@email.com", address: "Jl. Kenanga No. 7, Situbondo", tagihan: "Rp350.000" },
            { id: 10, name: "Irfan Hidayat", nik: "NIK001010", custNo: "CST0010", area: "Besuki", status: "Aktif", paket: "100 Mbps", phone: "081299990000", email: "irfan.hidayat@email.com", address: "Jl. Pelabuhan No. 22, Besuki", tagihan: "Rp450.000" }
        ];

        let currentSelectedCustomer = customersData[2] || customersData[0] || null;

        document.addEventListener("DOMContentLoaded", function() {
            renderTable(customersData);
            loadCustomerDetail(currentSelectedCustomer);

            document.getElementById('searchInput').addEventListener('input', applyFilters);
            document.getElementById('statusFilter').addEventListener('change', applyFilters);
            document.getElementById('areaFilter').addEventListener('change', applyFilters);
            document.getElementById('resetFilterBtn').addEventListener('click', resetFilters);
            document.getElementById('selectAll').addEventListener('change', toggleSelectAll);

            if (window.lucide) window.lucide.createIcons();
        });

        function renderTable(data) {
            const tbody = document.getElementById('customerTableBody');
            tbody.innerHTML = '';

            if (data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" class="text-center py-6 text-gray-400">Tidak ada data customer yang ditemukan.</td></tr>`;
                document.getElementById('totalVisibleCount').innerText = 0;
                return;
            }

            document.getElementById('totalVisibleCount').innerText = data.length;

            data.forEach((cust, idx) => {
                let badgeClass = 'bg-emerald-50 text-emerald-600';
                if (cust.status === 'Baru') badgeClass = 'bg-blue-50 text-blue-600';
                if (cust.status === 'Nonaktif') badgeClass = 'bg-red-50 text-red-600';

                const isSelected = currentSelectedCustomer && currentSelectedCustomer.custNo === cust.custNo;
                const tr = document.createElement('tr');
                tr.className = `customer-row cursor-pointer transition-colors hover:bg-blue-50/40 ${isSelected ? 'bg-blue-50/70 border-l-4 border-l-primary' : ''}`;

                tr.onclick = (e) => {
                    if (e.target.tagName !== 'INPUT') {
                        selectCustomer(cust.custNo);
                    }
                };

                tr.innerHTML = `
                    <td class="pl-4 py-3" onclick="event.stopPropagation()">
                        <input type="checkbox" class="row-checkbox rounded border-gray-300 text-primary focus:ring-primary/20" value="${cust.custNo}">
                    </td>
                    <td class="py-3 pr-3">
                        <div class="flex items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(cust.name)}&background=random" class="w-8 h-8 rounded-full shrink-0">
                            <span class="font-semibold text-dark">${cust.name}</span>
                        </div>
                    </td>
                    <td class="py-3 pr-3 text-secondary">${cust.nik}</td>
                    <td class="py-3 pr-3 font-bold text-primary">${cust.custNo}</td>
                    <td class="py-3 pr-3 text-dark">${cust.area}</td>
                    <td class="py-3 pr-3"><span class="px-2.5 py-1 rounded-full text-[11px] font-semibold ${badgeClass}">${cust.status}</span></td>
                    <td class="py-3 pr-3 text-dark font-medium">${cust.paket}</td>
                    <td class="py-3 pr-4 text-center">
                        <button type="button" class="p-1 rounded-md text-gray-400 hover:text-dark hover:bg-gray-100" onclick="selectCustomer('${cust.custNo}')">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            if (window.lucide) window.lucide.createIcons();
        }

        function selectCustomer(custNo) {
            const found = customersData.find(c => c.custNo === custNo);
            if (found) {
                currentSelectedCustomer = found;
                loadCustomerDetail(found);
                renderTable(getCurrentFilteredData());
            }
        }

        function loadCustomerDetail(cust) {
            document.getElementById('detailAvatar').src = `https://ui-avatars.com/api/?name=${encodeURIComponent(cust.name)}&background=0D6EFD&color=fff`;
            document.getElementById('detailName').innerText = cust.name;
            document.getElementById('detailCustNo').innerText = cust.custNo;

            let badgeClass = 'bg-emerald-50 text-emerald-600';
            if (cust.status === 'Baru') badgeClass = 'bg-blue-50 text-blue-600';
            if (cust.status === 'Nonaktif') badgeClass = 'bg-red-50 text-red-600';
            document.getElementById('detailStatusBadge').innerHTML = `<span class="px-2.5 py-1 rounded-full text-[11px] font-semibold ${badgeClass}">${cust.status}</span>`;
            document.getElementById('quickStatusSelect').value = cust.status;

            document.getElementById('valNik').innerText = cust.nik;
            document.getElementById('valPhone').innerText = cust.phone;
            document.getElementById('valEmail').innerText = cust.email;
            document.getElementById('valAddress').innerText = cust.address;

            document.getElementById('qiArea').innerText = cust.area;
            document.getElementById('qiPaket').innerText = cust.paket;
            document.getElementById('qiTagihan').innerText = cust.tagihan;

            const ticketTarget = document.getElementById('ticketTargetCustomer');
            if (ticketTarget) ticketTarget.value = `${cust.name} (${cust.custNo})`;
        }

        function applyFilters() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            const statusVal = document.getElementById('statusFilter').value;
            const areaVal = document.getElementById('areaFilter').value;

            const filtered = customersData.filter(cust => {
                const matchQuery = cust.name.toLowerCase().includes(query) ||
                                   cust.nik.toLowerCase().includes(query) ||
                                   cust.custNo.toLowerCase().includes(query) ||
                                   cust.address.toLowerCase().includes(query);
                const matchStatus = statusVal === "" || cust.status === statusVal;
                const matchArea = areaVal === "" || cust.area === areaVal;
                return matchQuery && matchStatus && matchArea;
            });

            renderTable(filtered);
        }

        function getCurrentFilteredData() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            const statusVal = document.getElementById('statusFilter').value;
            const areaVal = document.getElementById('areaFilter').value;

            return customersData.filter(cust => {
                const matchQuery = cust.name.toLowerCase().includes(query) ||
                                   cust.nik.toLowerCase().includes(query) ||
                                   cust.custNo.toLowerCase().includes(query) ||
                                   cust.address.toLowerCase().includes(query);
                const matchStatus = statusVal === "" || cust.status === statusVal;
                const matchArea = areaVal === "" || cust.area === areaVal;
                return matchQuery && matchStatus && matchArea;
            });
        }

        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('areaFilter').value = '';
            renderTable(customersData);
        }

        function toggleSelectAll(e) {
            document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = e.target.checked);
        }

        function switchTab(tabName) {
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('border-primary', 'text-primary');
                btn.classList.add('border-transparent', 'text-secondary');
            });
            event.currentTarget.classList.add('border-primary', 'text-primary');
            event.currentTarget.classList.remove('border-transparent', 'text-secondary');

            document.querySelectorAll('.tab-pane').forEach(p => p.classList.add('hidden'));
            const target = document.getElementById(`tab-pane-${tabName}`);
            if (target) target.classList.remove('hidden');
        }

        function updateCustomerStatusFromPanel(newStatus) {
            if (currentSelectedCustomer) {
                currentSelectedCustomer.status = newStatus;
                renderTable(getCurrentFilteredData());
                loadCustomerDetail(currentSelectedCustomer);
                alert(`Simulasi: Status customer ${currentSelectedCustomer.name} diubah menjadi ${newStatus}.`);
            }
        }

        // Modal Handlers
        function openAddModal() { document.getElementById('modal-add-customer').classList.remove('hidden'); }
        function closeAddModal() { document.getElementById('modal-add-customer').classList.add('hidden'); }
        document.getElementById('btn-open-add-modal').addEventListener('click', openAddModal);

        function openAssignTicketModal() { document.getElementById('modal-assign-ticket').classList.remove('hidden'); }
        function closeAssignModal() { document.getElementById('modal-assign-ticket').classList.add('hidden'); }

        function openDeactivateModal() { document.getElementById('modal-deactivate').classList.remove('hidden'); }
        function closeDeactivateModal() { document.getElementById('modal-deactivate').classList.add('hidden'); }

        function handleSimulatedSubmit(event, type) {
            event.preventDefault();
            alert('Simulasi Frontend: Data berhasil disimpan!');
            closeAddModal();
        }

        function handleTicketSubmit(event) {
            event.preventDefault();
            alert('Simulasi Frontend: Ticket berhasil di-assign!');
            closeAssignModal();
        }

        function confirmDeactivate() {
            if (currentSelectedCustomer) {
                currentSelectedCustomer.status = 'Nonaktif';
                renderTable(getCurrentFilteredData());
                loadCustomerDetail(currentSelectedCustomer);
                alert(`Simulasi: Customer ${currentSelectedCustomer.name} dinonaktifkan.`);
                closeDeactivateModal();
            }
        }

        function resetDetailPanel() {
            alert('Panel detail disegarkan.');
        }
    </script>
</x-app-layout>
