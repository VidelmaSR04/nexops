<x-app-layout>
    <div class="space-y-6">

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700" role="status">
                {{ session('success') }}
            </div>
        @endif

        {{-- 1. HEADER CUSTOMERS --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-dark tracking-tight">Data OPD</h1>
                <p class="text-secondary text-sm mt-0.5">Kelola OPD serta informasi pelapor dan kontaknya</p>
            </div>
            <div>
                <button type="button" id="btn-open-add-modal"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-white text-sm font-medium hover:bg-blue-700 shadow-sm transition-colors cursor-pointer">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    <span>Tambah OPD</span>
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
                    <p class="text-2xl font-bold text-dark mt-0.5">{{ number_format($stats['total']) }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i data-lucide="user-check" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Pelanggan Aktif</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">{{ number_format($stats['active']) }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <i data-lucide="user-plus" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Pelanggan Baru</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">{{ number_format($stats['new']) }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                    <i data-lucide="user-minus" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Pelanggan Nonaktif</p>
                    <p class="text-2xl font-bold text-dark mt-0.5">{{ number_format($stats['inactive']) }}</p>
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
                                   placeholder="Cari nama, nomor pelanggan, atau alamat..."
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
                                <th class="py-3 pr-3">OPD</th>
                                <th class="py-3 pr-3">Nama Pelapor</th>
                                <th class="py-3 pr-3">Kontak Pelapor</th>
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
                    <p class="text-secondary">Menampilkan <span id="totalVisibleCount">0</span> dari <span id="totalCustomerCount">{{ $stats['total'] }}</span> total data</p>
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
                    <h3 class="text-base font-bold text-dark">Detail OPD</h3>
                    <button type="button" onclick="resetDetailPanel()" class="p-1 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                {{-- Profil Ringkas --}}
                <div class="flex items-center gap-3.5 p-3.5 rounded-xl bg-surface border border-border/80 mb-4">
                    <img id="detailAvatar" src="https://ui-avatars.com/api/?name=Budi+Santoso&background=0D6EFD&color=fff" alt="Avatar" class="w-12 h-12 rounded-full shrink-0">
                    <div class="flex-1 min-w-0">
                        <h4 id="detailName" class="font-bold text-dark text-sm truncate">Pilih OPD</h4>
                        <span id="detailCustNo" class="inline-block px-2 py-0.5 mt-0.5 rounded text-[11px] font-semibold bg-blue-50 text-primary border border-blue-200">-</span>
                    </div>
                    <div id="detailStatusBadge">
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-500">-</span>
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
                            <strong id="qiArea" class="text-sm">-</strong>
                        </div>
                        <div>
                            <span class="opacity-75 text-[10px] block">Paket</span>
                            <strong id="qiPaket" class="text-sm">-</strong>
                        </div>
                        <div>
                            <span class="opacity-75 text-[10px] block">Tagihan Terakhir</span>
                            <strong id="qiTagihan" class="text-sm">-</strong>
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
                            <span class="text-secondary">Nama Pelapor</span>
                            <span id="valReporter" class="font-medium text-dark">-</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-100">
                            <span class="text-secondary">Kontak Pelapor</span>
                            <span id="valReporterContact" class="font-medium text-dark">-</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-100">
                            <span class="text-secondary">Email</span>
                            <span id="valEmail" class="font-medium text-dark">-</span>
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
                <h3 id="customer-form-title" class="text-base font-bold text-dark">Tambah OPD</h3>
                <button type="button" onclick="closeAddModal()" class="p-1 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form id="customer-form" method="POST" action="{{ route('customers.store') }}" class="p-5 space-y-3.5 text-xs">
                @csrf
                <input type="hidden" name="_method" id="customer-form-method" value="PUT" disabled>
                <input type="hidden" name="_customer_id" id="customer-form-id" value="{{ old('_customer_id') }}">
                <input type="hidden" name="type" value="opd">
                @if ($errors->any())
                    <div class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-red-700">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                <div>
                    <label class="block font-medium text-dark mb-1">Nama OPD</label>
                    <input id="customer-name" type="text" name="name" value="{{ old('name') }}" required maxlength="255" placeholder="Contoh: Dinas Komunikasi dan Informatika" class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1">Nama Pelapor</label>
                        <input id="customer-reporter-name" type="text" name="reporter_name" value="{{ old('reporter_name') }}" maxlength="150" placeholder="Nama staf OPD" class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Kontak Pelapor</label>
                        <input id="customer-reporter-contact" type="text" name="reporter_contact" value="{{ old('reporter_contact') }}" maxlength="50" placeholder="No. HP / WhatsApp" class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1">No. Pelanggan</label>
                    <p class="rounded-xl border border-border bg-gray-50 px-3 py-2 text-xs text-secondary">Kode dibuat otomatis saat disimpan</p>
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1">Area</label>
                    <input id="customer-area" type="text" name="area" value="{{ old('area') }}" placeholder="Area OPD" class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1">Alamat Lengkap</label>
                    <textarea id="customer-address" name="address" rows="2" placeholder="Alamat kantor OPD..." class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1">Paket Layanan</label>
                        <select id="customer-package" name="package_name" class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                            <option value="">Tidak ada</option>
                            <option value="50 Mbps">50 Mbps</option>
                            <option value="100 Mbps">100 Mbps</option>
                            <option value="200 Mbps">200 Mbps</option>
                            <option value="300 Mbps">300 Mbps</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Status</label>
                        <select id="customer-status" name="status" class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                            <option value="Aktif">Aktif</option>
                            <option value="Baru">Baru</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="pt-3 border-t border-border flex items-center justify-end gap-2">
                    <button type="button" onclick="closeAddModal()" class="px-3.5 py-2 rounded-xl border border-border text-secondary hover:bg-gray-50 font-medium">Batal</button>
                    <button id="customer-form-submit" type="submit" class="px-4 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-blue-700 shadow-sm">Simpan OPD</button>
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
        const customersData = serverData || [];
        const customerBaseUrl = @json(url('/customers'));
        const customersPerPage = 10;
        const customerCountLabel = document.getElementById('totalVisibleCount').parentElement;
        let customerPage = 1;
        let customerDetailRequest = 0;

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, char => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
            })[char]);
        }

        let currentSelectedCustomer = customersData[2] || customersData[0] || null;

        document.addEventListener("DOMContentLoaded", function() {
            renderTable(customersData);
            if (currentSelectedCustomer) loadCustomerDetail(currentSelectedCustomer);

            document.getElementById('searchInput').addEventListener('input', () => {
                customerPage = 1;
                applyFilters();
            });
            document.getElementById('statusFilter').addEventListener('change', () => {
                customerPage = 1;
                applyFilters();
            });
            document.getElementById('areaFilter').addEventListener('change', () => {
                customerPage = 1;
                applyFilters();
            });
            document.getElementById('resetFilterBtn').addEventListener('click', resetFilters);
            document.getElementById('selectAll').addEventListener('change', toggleSelectAll);
            document.getElementById('totalVisibleCount').parentElement.nextElementSibling.addEventListener('click', (event) => {
                const button = event.target.closest('[data-customer-page]');
                if (!button || button.disabled) return;
                customerPage = Number(button.dataset.customerPage);
                renderTable(getCurrentFilteredData());
            });

            if (window.lucide) window.lucide.createIcons();
        });

        async function requestJson(url, method, data) {
            const response = await fetch(url, {
                method,
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: data ? JSON.stringify(data) : undefined,
            });
            const payload = await response.json().catch(() => ({}));
            if (!response.ok) {
                const firstError = payload.errors ? Object.values(payload.errors).flat()[0] : null;
                throw new Error(firstError || payload.message || 'Permintaan gagal diproses.');
            }
            return payload;
        }

        function renderTable(data) {
            const tbody = document.getElementById('customerTableBody');
            tbody.innerHTML = '';
            const pageCount = Math.max(1, Math.ceil(data.length / customersPerPage));
            customerPage = Math.min(Math.max(customerPage, 1), pageCount);
            const start = (customerPage - 1) * customersPerPage;
            const visibleCustomers = data.slice(start, start + customersPerPage);

            if (data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="9" class="text-center py-6 text-gray-400">Tidak ada data OPD yang ditemukan.</td></tr>`;
                document.getElementById('totalVisibleCount').innerText = 0;
                return;
            }

            updateCustomerCount(start + 1, start + visibleCustomers.length, data.length);

            visibleCustomers.forEach((cust) => {
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
                        <input type="checkbox" class="row-checkbox rounded border-gray-300 text-primary focus:ring-primary/20" value="${escapeHtml(cust.custNo)}">
                    </td>
                    <td class="py-3 pr-3">
                        <div class="flex items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(cust.name)}&background=random" class="w-8 h-8 rounded-full shrink-0">
                            <span class="font-semibold text-dark">${escapeHtml(cust.name)}</span>
                        </div>
                    </td>
                    <td class="py-3 pr-3 text-dark">${escapeHtml(cust.reporter_name || '-')}</td>
                    <td class="py-3 pr-3 text-dark">${escapeHtml(cust.reporter_contact || '-')}</td>
                    <td class="py-3 pr-3 font-bold text-primary">${cust.custNo}</td>
                    <td class="py-3 pr-3 text-dark">${cust.area}</td>
                    <td class="py-3 pr-3"><span class="px-2.5 py-1 rounded-full text-[11px] font-semibold ${badgeClass}">${cust.status}</span></td>
                    <td class="py-3 pr-3 text-dark font-medium">${cust.paket}</td>
                    <td class="py-3 pr-4 text-center">
                        <div class="flex justify-center gap-1">
                            <button type="button" class="p-1 rounded-md text-gray-400 hover:text-dark hover:bg-gray-100" onclick="event.stopPropagation(); selectCustomer('${cust.custNo}')" aria-label="Lihat detail OPD">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                            <button type="button" class="p-1 rounded-md text-gray-400 hover:text-primary hover:bg-blue-50" onclick="event.stopPropagation(); openEditModal(${Number(cust.id)})" aria-label="Edit OPD">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            if (window.lucide) window.lucide.createIcons();
            renderPagination(pageCount);
        }

        function updateCustomerCount(from, to, total) {
            const visibleCount = from === 0 ? 0 : (to - from + 1);
            customerCountLabel.innerHTML =
                'Menampilkan <span id="totalVisibleCount">' + visibleCount + '</span> dari ' + total + ' total data';
        }

        function renderPagination(pageCount) {
            const container = document.getElementById('totalVisibleCount').parentElement.nextElementSibling;
            const buttonClass = 'w-7 h-7 rounded-lg border border-border text-secondary hover:bg-gray-50 flex items-center justify-center';
            const activeClass = 'w-7 h-7 rounded-lg bg-primary text-white font-medium flex items-center justify-center';
            const disabledClass = 'opacity-40 cursor-not-allowed';
            let buttons = '<button type="button" data-customer-page="' + (customerPage - 1) + '" aria-label="Halaman sebelumnya" class="' + buttonClass + (customerPage <= 1 ? ' ' + disabledClass : '') + '"' + (customerPage <= 1 ? ' disabled' : '') + '><i data-lucide="chevron-left" class="w-3.5 h-3.5"></i></button>';
            for (let page = 1; page <= pageCount; page++) {
                buttons += '<button type="button" data-customer-page="' + page + '" class="' + (page === customerPage ? activeClass : buttonClass) + '"' + (page === customerPage ? ' aria-current="page"' : '') + '>' + page + '</button>';
            }
            buttons += '<button type="button" data-customer-page="' + (customerPage + 1) + '" aria-label="Halaman berikutnya" class="' + buttonClass + (customerPage >= pageCount ? ' ' + disabledClass : '') + '"' + (customerPage >= pageCount ? ' disabled' : '') + '><i data-lucide="chevron-right" class="w-3.5 h-3.5"></i></button>';
            container.innerHTML = buttons;
            if (window.lucide) window.lucide.createIcons();
        }

        function selectCustomer(custNo) {
            const found = customersData.find(c => c.custNo === custNo);
            if (found) {
                currentSelectedCustomer = found;
                loadCustomerDetail(found);
                loadCustomerRecords(found);
                renderTable(getCurrentFilteredData());
            }
        }

        function loadCustomerDetail(cust) {
            if (!cust) {
                document.getElementById('detailName').innerText = '';
                document.getElementById('detailCustNo').innerText = '';
                document.getElementById('valReporter').innerText = '';
                document.getElementById('valReporterContact').innerText = '';
                document.getElementById('valEmail').innerText = '';
                document.getElementById('valAddress').innerText = '';
                document.getElementById('qiArea').innerText = '';
                document.getElementById('qiPaket').innerText = '';
                document.getElementById('qiTagihan').innerText = '';
                document.getElementById('ticketTargetCustomer').value = '';
                return;
            }

            document.getElementById('detailAvatar').src = `https://ui-avatars.com/api/?name=${encodeURIComponent(cust.name)}&background=0D6EFD&color=fff`;
            document.getElementById('detailName').innerText = cust.name;
            document.getElementById('detailCustNo').innerText = cust.custNo;

            let badgeClass = 'bg-emerald-50 text-emerald-600';
            if (cust.status === 'Baru') badgeClass = 'bg-blue-50 text-blue-600';
            if (cust.status === 'Nonaktif') badgeClass = 'bg-red-50 text-red-600';
            document.getElementById('detailStatusBadge').innerHTML = `<span class="px-2.5 py-1 rounded-full text-[11px] font-semibold ${badgeClass}">${cust.status}</span>`;
            document.getElementById('quickStatusSelect').value = cust.status;

            document.getElementById('valReporter').innerText = cust.reporter_name || '-';
            document.getElementById('valReporterContact').innerText = cust.reporter_contact || '-';
            document.getElementById('valEmail').innerText = cust.email;
            document.getElementById('valAddress').innerText = cust.address;

            document.getElementById('qiArea').innerText = cust.area;
            document.getElementById('qiPaket').innerText = cust.paket;
            document.getElementById('qiTagihan').innerText = cust.tagihan || '—';

            const ticketTarget = document.getElementById('ticketTargetCustomer');
            if (ticketTarget) ticketTarget.value = `${cust.name} (${cust.custNo})`;
        }

        async function loadCustomerRecords(customer) {
            const requestId = ++customerDetailRequest;
            try {
                const response = await fetch(customerBaseUrl + '/' + encodeURIComponent(customer.id), {
                    headers: { 'Accept': 'application/json' },
                });
                const payload = await response.json().catch(() => ({}));
                if (!response.ok) throw new Error(payload.message || 'Detail pelanggan gagal dimuat.');
                if (requestId !== customerDetailRequest || currentSelectedCustomer?.id !== customer.id) return;

                const history = payload.data.ticket_history || [];
                const historyPane = document.getElementById('tab-pane-riwayat');
                historyPane.innerHTML = history.length
                    ? '<div class="space-y-3 relative before:absolute before:inset-0 before:left-2 before:w-0.5 before:bg-gray-200">'
                        + history.map((ticket) => '<div class="relative flex items-start gap-3 pl-6">'
                            + '<span class="absolute left-1 top-1 w-2.5 h-2.5 rounded-full bg-primary ring-4 ring-white"></span>'
                            + '<div><p class="font-semibold text-dark">Ticket ' + escapeHtml(ticket.ticket_number) + '</p>'
                            + '<p class="text-[11px] text-secondary">' + escapeHtml(ticket.title) + ' · ' + escapeHtml(ticket.status) + '</p>'
                            + '<p class="text-[11px] text-secondary">' + escapeHtml(ticket.created_at || '') + '</p></div></div>').join('')
                        + '</div>'
                    : '<p class="py-6 text-center text-secondary">Belum ada riwayat tiket.</p>';

                const billingBody = document.querySelector('#tab-pane-tagihan tbody');
                billingBody.innerHTML = '<tr><td colspan="4" class="p-3 text-secondary">Data riwayat tagihan belum tersedia.</td></tr>';

                document.getElementById('tab-pane-dokumen').innerHTML =
                    '<p class="py-6 text-center text-secondary">Belum ada dokumen pelanggan yang tersimpan.</p>';

                const ticketCountRow = Array.from(document.querySelectorAll('#tab-pane-detail > div'))
                    .find((row) => row.firstElementChild?.textContent.trim() === 'Ticket Aktif');
                if (ticketCountRow) {
                    ticketCountRow.lastElementChild.textContent =
                        (payload.data.customer.active_tickets_count || 0) + ' Ticket';
                }
            } catch (error) {
                if (requestId === customerDetailRequest) window.alert(error.message);
            }
        }

        function applyFilters() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            const statusVal = document.getElementById('statusFilter').value;
            const areaVal = document.getElementById('areaFilter').value;

            const filtered = customersData.filter(cust => {
                const matchQuery = cust.name.toLowerCase().includes(query) ||
                                   String(cust.reporter_name || '').toLowerCase().includes(query) ||
                                   String(cust.reporter_contact || '').toLowerCase().includes(query) ||
                                   cust.custNo.toLowerCase().includes(query) ||
                                   cust.address.toLowerCase().includes(query) ||
                                   cust.email.toLowerCase().includes(query);
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
                                   String(cust.reporter_name || '').toLowerCase().includes(query) ||
                               String(cust.reporter_contact || '').toLowerCase().includes(query) ||
                               cust.custNo.toLowerCase().includes(query) ||
                               cust.address.toLowerCase().includes(query) ||
                               cust.email.toLowerCase().includes(query);
                const matchStatus = statusVal === "" || cust.status === statusVal;
                const matchArea = areaVal === "" || cust.area === areaVal;
                return matchQuery && matchStatus && matchArea;
            });
        }

        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('areaFilter').value = '';
            customerPage = 1;
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

        async function updateCustomerStatusFromPanel(newStatus) {
            if (!currentSelectedCustomer) return;
            try {
                await requestJson(
                    customerBaseUrl + '/' + encodeURIComponent(currentSelectedCustomer.id) + '/status',
                    'PATCH',
                    { status: newStatus },
                );
                window.location.reload();
            } catch (error) {
                document.getElementById('quickStatusSelect').value = currentSelectedCustomer.status;
                window.alert(error.message);
            }
        }

        // Modal Handlers
        const customerForm = document.getElementById('customer-form');
        const customerMethodInput = document.getElementById('customer-form-method');
        const customerUpdateUrl = @json(route('customers.update', ['customer' => '__ID__']));

        function openAddModal() {
            customerForm.reset();
            customerForm.action = @json(route('customers.store'));
            customerMethodInput.disabled = true;
            document.getElementById('customer-form-id').value = '';
            document.getElementById('customer-form-title').innerText = 'Tambah OPD';
            document.getElementById('customer-form-submit').innerText = 'Simpan OPD';
            document.getElementById('modal-add-customer').classList.remove('hidden');
        }

        function openEditModal(id) {
            const customer = customersData.find(item => Number(item.id) === Number(id));
            if (!customer) return;

            const blank = value => value === '-' || value == null ? '' : value;
            customerForm.action = customerUpdateUrl.replace('__ID__', customer.id);
            customerMethodInput.disabled = false;
            document.getElementById('customer-form-id').value = customer.id;
            document.getElementById('customer-form-title').innerText = `Edit OPD: ${customer.name}`;
            document.getElementById('customer-form-submit').innerText = 'Simpan Perubahan';
            document.getElementById('customer-name').value = customer.name;
            document.getElementById('customer-reporter-name').value = blank(customer.reporter_name);
            document.getElementById('customer-reporter-contact').value = blank(customer.reporter_contact);
            document.getElementById('customer-area').value = blank(customer.area);
            document.getElementById('customer-address').value = blank(customer.address);
            document.getElementById('customer-package').value = blank(customer.package_name);
            document.getElementById('customer-status').value = customer.status;
            document.getElementById('modal-add-customer').classList.remove('hidden');
        }

        function closeAddModal() { document.getElementById('modal-add-customer').classList.add('hidden'); }
        document.getElementById('btn-open-add-modal').addEventListener('click', openAddModal);
        @if ($errors->any())
            const failedCustomerId = @json(old('_customer_id'));
            const previousCustomerValues = @json(old());
            if (failedCustomerId) openEditModal(failedCustomerId);
            else openAddModal();
            ['name', 'reporter_name', 'reporter_contact', 'area', 'address', 'package_name', 'status'].forEach(name => {
                if (Object.prototype.hasOwnProperty.call(previousCustomerValues, name)) {
                    customerForm.elements[name].value = previousCustomerValues[name] || '';
                }
            });
        @endif

        function openAssignTicketModal() { document.getElementById('modal-assign-ticket').classList.remove('hidden'); }
        function closeAssignModal() { document.getElementById('modal-assign-ticket').classList.add('hidden'); }

        function openDeactivateModal() { document.getElementById('modal-deactivate').classList.remove('hidden'); }
        function closeDeactivateModal() { document.getElementById('modal-deactivate').classList.add('hidden'); }

        function handleTicketSubmit(event) {
            event.preventDefault();
            alert('Simulasi Frontend: Ticket berhasil di-assign!');
            closeAssignModal();
        }

        async function confirmDeactivate() {
            if (!currentSelectedCustomer) return;
            try {
                await requestJson(
                    customerBaseUrl + '/' + encodeURIComponent(currentSelectedCustomer.id),
                    'DELETE',
                );
                window.location.reload();
            } catch (error) {
                window.alert(error.message);
            }
        }

        function resetDetailPanel() {
            alert('Panel detail disegarkan.');
        }
    </script>
</x-app-layout>
