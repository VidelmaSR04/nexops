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
                    <p id="totalCustomerStat" class="text-2xl font-bold text-dark mt-0.5">{{ number_format($stats['total']) }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i data-lucide="user-check" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Pelanggan Aktif</p>
                    <p id="activeCustomerStat" class="text-2xl font-bold text-dark mt-0.5">{{ number_format($stats['active']) }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <i data-lucide="user-plus" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Pelanggan Baru</p>
                    <p id="newCustomerStat" class="text-2xl font-bold text-dark mt-0.5">{{ number_format($stats['new']) }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                    <i data-lucide="user-minus" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Pelanggan Nonaktif</p>
                    <p id="inactiveCustomerStat" class="text-2xl font-bold text-dark mt-0.5">{{ number_format($stats['inactive']) }}</p>
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
                                   placeholder="Cari nama, kode pelanggan, atau alamat..."
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

                        <select id="monthFilter" aria-label="Filter bulan pendaftaran" class="text-xs rounded-xl border border-border bg-white px-3 py-2 text-dark focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <option value="">Semua Bulan</option>
                            <option value="01">Januari</option>
                            <option value="02">Februari</option>
                            <option value="03">Maret</option>
                            <option value="04">April</option>
                            <option value="05">Mei</option>
                            <option value="06">Juni</option>
                            <option value="07">Juli</option>
                            <option value="08">Agustus</option>
                            <option value="09">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>

                        <select id="yearFilter" aria-label="Filter tahun pendaftaran" class="text-xs rounded-xl border border-border bg-white px-3 py-2 text-dark focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <option value="">Semua Tahun</option>
                        </select>

                        <input id="dateFilter" type="date" aria-label="Filter tanggal pendaftaran" title="Filter tanggal pendaftaran"
                               class="text-xs rounded-xl border border-border bg-white px-3 py-2 text-dark focus:outline-none focus:ring-2 focus:ring-primary/20">

                        <button type="button" id="resetFilterBtn" class="px-3 py-2 rounded-xl border border-border text-xs font-medium text-secondary hover:bg-gray-50 transition-colors">
                            Reset
                        </button>
                        <button type="button" id="deleteSelectedCustomersBtn" disabled
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-red-200 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors disabled:cursor-not-allowed disabled:opacity-40"
                                title="Hapus data yang dipilih" aria-label="Hapus data yang dipilih">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                            <span id="deleteSelectedCustomersLabel">Hapus</span>
                        </button>
                    </div>
                </div>

                {{-- Tabel Data --}}
                <div class="max-h-[480px] overflow-auto">
                    <table class="w-full table-fixed text-xs text-left">
                        <colgroup>
                            <col style="width: 4%">
                            <col style="width: 47%">
                            <col style="width: 8%">
                            <col style="width: 8%">
                            <col style="width: 8%">
                            <col style="width: 7%">
                            <col style="width: 6%">
                            <col style="width: 5%">
                            <col style="width: 4%">
                            <col style="width: 3%">
                        </colgroup>
                        <thead>
                            <tr class="text-secondary bg-[#FAFCFF] border-b border-border font-medium text-[11px] uppercase tracking-wider">
                                <th class="sticky top-0 z-20 bg-[#FAFCFF] pl-4 py-3 w-8">
                                    <input id="selectAll" type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary/20">
                                </th>
                                <th class="sticky top-0 z-20 bg-[#FAFCFF] py-3 pr-3">OPD</th>
                                <th class="sticky top-0 z-20 bg-[#FAFCFF] py-3 pr-3">Nama Pelapor</th>
                                <th class="sticky top-0 z-20 bg-[#FAFCFF] py-3 pr-3">Kontak Pelapor</th>
                                <th class="sticky top-0 z-20 bg-[#FAFCFF] py-3 pr-3">Kode Pelanggan</th>
                                <th class="sticky top-0 z-20 bg-[#FAFCFF] py-3 pr-3">Tanggal</th>
                                <th class="sticky top-0 z-20 bg-[#FAFCFF] py-3 pr-3">Area</th>
                                <th class="sticky top-0 z-20 bg-[#FAFCFF] py-3 pr-3">Status</th>
                                <th class="sticky top-0 z-20 bg-[#FAFCFF] py-3 pr-3">Paket</th>
                                <th class="sticky top-0 z-20 bg-[#FAFCFF] py-3 pr-4 text-center w-8">Aksi</th>
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
                    <div class="flex items-center gap-1" aria-label="Navigasi halaman data OPD"></div>
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
        <div class="bg-white rounded-2xl border border-border shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
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
                <div id="customer-opd-selector">
                    <label for="customer-name-picker" class="block font-medium text-dark mb-1">Nama OPD</label>
                    <div class="relative">
                        <input id="customer-name" type="hidden" name="name" value="{{ old('name') }}">
                        <button id="customer-name-picker" type="button" aria-haspopup="listbox" aria-expanded="false"
                                class="flex w-full items-center justify-between rounded-xl border border-border bg-white px-3 py-2 text-left text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <span id="customer-name-label">{{ old('name', 'Pilih OPD dari data OpdSeeder') }}</span>
                            <i data-lucide="chevron-down" class="h-4 w-4 shrink-0 text-secondary"></i>
                        </button>
                        <div id="customer-opd-dropdown" class="absolute left-0 right-0 top-full z-[60] mt-1 hidden rounded-xl border border-border bg-white p-2 shadow-xl">
                            <label for="customer-opd-search" class="sr-only">Cari nama OPD</label>
                            <input id="customer-opd-search" type="search" autocomplete="off" placeholder="Cari nama OPD..."
                                   class="mb-2 w-full rounded-lg border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <div id="customer-opd-options" role="listbox" aria-label="Daftar OPD" class="max-h-44 overflow-y-auto"></div>
                        </div>
                    </div>
                    <p class="mt-1 text-[11px] text-secondary">Pilih OPD dari data OpdSeeder. OPD yang sama dapat dipilih kembali untuk membuat data baru.</p>
                </div>
                <div id="customer-name-locked" class="hidden">
                    <label class="block font-medium text-dark mb-1">Nama OPD</label>
                    <input id="customer-name-display" type="text" disabled class="w-full rounded-xl border border-border bg-gray-100 px-3 py-2 text-xs text-gray-500">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1">Nama Pelapor</label>
                        <input id="customer-reporter-name" type="text" name="reporter_name" value="{{ old('reporter_name') }}" required maxlength="150" placeholder="Nama staf OPD" class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Kontak WhatsApp Pelapor</label>
                        <input id="customer-reporter-contact" type="tel" name="reporter_contact" value="{{ old('reporter_contact') }}" required maxlength="14" inputmode="numeric" pattern="[0-9]{1,14}" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 14)" placeholder="No. HP / WhatsApp" class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>
                </div>
                <div>
                    <label for="customer-registration-date" class="block font-medium text-dark mb-1">Tanggal Pendaftaran</label>
                    <input id="customer-registration-date" type="date" name="registration_date" value="{{ old('registration_date', now()->toDateString()) }}" required
                           class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1">Kode Pelanggan</label>
                    <p id="customer-code-display" class="rounded-xl border border-border bg-gray-100 px-3 py-2 text-xs text-gray-500">Kode otomatis muncul setelah nama OPD dan tanggal dipilih</p>
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1">Area</label>
                    <input id="customer-area" type="text" name="area" value="{{ old('area') }}" required placeholder="Pilih OPD tersimpan atau isi area secara manual" class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <div class="relative mt-2">
                        <label for="customer-area-search" class="sr-only">Cari area atau alamat</label>
                        <input id="customer-area-search" type="search" autocomplete="off" placeholder="Cari area, kecamatan, atau alamat OPD..."
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <div id="customer-area-options" class="absolute left-0 right-0 top-full z-[60] mt-1 hidden max-h-48 overflow-y-auto rounded-xl border border-border bg-white p-1 shadow-xl" role="listbox" aria-label="Hasil pencarian lokasi"></div>
                    </div>
                    <p class="mt-1 text-[11px] text-secondary">Dengan Google Maps interaktif, klik titik di peta untuk mengisi area, alamat, dan koordinat. Jika key belum diatur, gunakan pencarian lokasi di bawah peta.</p>
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1">Titik Lokasi Kantor OPD (Kabupaten Situbondo)</label>
                    <p class="mb-2 text-[11px] text-secondary">Klik titik peta untuk mengisi lokasi otomatis. Google Maps JavaScript API dan Geocoding API perlu aktif pada key browser.</p>
                    <div id="customer-location-map" class="h-56 w-full overflow-hidden rounded-xl border border-border">
                    </div>
                    <div class="grid grid-cols-2 gap-3 mt-2">
                        <input id="customer-latitude" type="number" name="latitude" step="any" min="-8.05" max="-7.3" required placeholder="Latitude"
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                        <input id="customer-longitude" type="number" name="longitude" step="any" min="113.5" max="114.65" required placeholder="Longitude"
                               class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                    </div>
                    <p id="customer-location-status" class="mt-1 min-h-4 text-[11px] text-secondary" role="status">Pilih titik lokasi di Kabupaten Situbondo.</p>
                </div>
                <div>
                    <label for="customer-address" class="block font-medium text-dark mb-1">Alamat Lengkap</label>
                    <textarea id="customer-address" name="address" rows="2" required placeholder="Alamat akan terisi setelah titik dipilih; periksa dan lengkapi bila perlu." class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">{{ old('address') }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-dark mb-1">Paket Layanan</label>
                        <select id="customer-package" name="package_name" required class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                            <option value="">Pilih paket layanan</option>
                            <option value="50 Mbps">50 Mbps</option>
                            <option value="100 Mbps">100 Mbps</option>
                            <option value="200 Mbps">200 Mbps</option>
                            <option value="300 Mbps">300 Mbps</option>
                            <option value="500 Mbps">500 Mbps</option>
                            <option value="750 Mbps">750 Mbps</option>
                            <option value="1 Gbps">1 Gbps</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium text-dark mb-1">Status</label>
                        <select id="customer-status" name="status" required class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
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
    <div id="customer-toast" role="status" class="fixed bottom-5 right-5 z-[70] hidden max-w-sm rounded-xl bg-dark px-4 py-3 text-xs text-white shadow-xl"></div>

    <script>
        const serverData = @json($customersData ?? null);
        const customersData = serverData || [];
        const customerBaseUrl = @json(url('/customers'));
        const customersPerPage = 20;
        let customerPage = 1;
        let customerDetailRequest = 0;
        const selectedCustomerIds = new Set();
        let customerToastTimer;

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, char => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
            })[char]);
        }

        function populateYearFilter() {
            const filter = document.getElementById('yearFilter');
            [...new Set(customersData.map(customer => customer.registration_date?.slice(0, 4)).filter(Boolean))]
                .sort((a, b) => b.localeCompare(a))
                .forEach(year => filter.add(new Option(year, year)));
        }

        function formatRegistrationDate(value) {
            if (!value) return '-';
            const [year, month, day] = value.split('-').map(Number);
            return new Intl.DateTimeFormat('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
            }).format(new Date(year, month - 1, day));
        }

        let currentSelectedCustomer = customersData[2] || customersData[0] || null;

        document.addEventListener("DOMContentLoaded", function() {
            renderTable(customersData);
            populateAreaFilter();
            populateYearFilter();
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
            ['monthFilter', 'yearFilter', 'dateFilter'].forEach(id => {
                document.getElementById(id).addEventListener('change', () => {
                    customerPage = 1;
                    applyFilters();
                });
            });
            document.getElementById('resetFilterBtn').addEventListener('click', resetFilters);
            document.getElementById('selectAll').addEventListener('change', toggleSelectAll);
            document.getElementById('deleteSelectedCustomersBtn').addEventListener('click', deleteSelectedCustomers);
            document.getElementById('customerTableBody').addEventListener('change', (event) => {
                if (!event.target.matches('.row-checkbox')) return;
                const customerId = Number(event.target.value);
                if (event.target.checked) selectedCustomerIds.add(customerId);
                else selectedCustomerIds.delete(customerId);
                updateSelectionControls(getCurrentFilteredData());
            });
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
                tbody.innerHTML = `<tr><td colspan="10" class="text-center py-6 text-gray-400">Tidak ada data OPD yang ditemukan.</td></tr>`;
                updateCustomerCount(0, 0, 0);
                renderPagination(pageCount);
                updateSelectionControls(data);
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
                        <input type="checkbox" class="row-checkbox rounded border-gray-300 text-primary focus:ring-primary/20" value="${Number(cust.id)}" ${selectedCustomerIds.has(Number(cust.id)) ? 'checked' : ''} aria-label="Pilih ${escapeHtml(cust.name)}">
                    </td>
                    <td class="py-3 pr-3 overflow-hidden text-ellipsis whitespace-nowrap" title="${escapeHtml(cust.name)}">
                        <div class="flex items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(cust.name)}&background=random" class="w-8 h-8 rounded-full shrink-0">
                            <span class="font-semibold text-dark overflow-hidden text-ellipsis">${escapeHtml(cust.name)}</span>
                        </div>
                    </td>
                    <td class="py-3 pr-3 text-dark overflow-hidden text-ellipsis whitespace-nowrap">${escapeHtml(cust.reporter_name || '-')}</td>
                    <td class="py-3 pr-3 text-dark overflow-hidden text-ellipsis whitespace-nowrap">${escapeHtml(cust.reporter_contact || '-')}</td>
                    <td class="py-3 pr-3 font-bold text-primary overflow-hidden text-ellipsis whitespace-nowrap">${escapeHtml(cust.custNo)}</td>
                    <td class="py-3 pr-3 text-dark overflow-hidden text-ellipsis whitespace-nowrap">${formatRegistrationDate(cust.registration_date)}</td>
                    <td class="py-3 pr-3 text-dark overflow-hidden text-ellipsis whitespace-nowrap">${escapeHtml(cust.area || '-')}</td>
                    <td class="py-3 pr-3 overflow-hidden text-ellipsis whitespace-nowrap"><span class="px-2.5 py-1 rounded-full text-[11px] font-semibold ${badgeClass}">${escapeHtml(cust.status)}</span></td>
                    <td class="py-3 pr-3 text-dark font-medium overflow-hidden text-ellipsis whitespace-nowrap">${escapeHtml(cust.paket)}</td>
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
            updateSelectionControls(data);
        }

        function updateCustomerCount(from, to, total) {
            const visibleCount = from === 0 ? 0 : (to - from + 1);
            document.getElementById('totalVisibleCount').textContent = visibleCount;
            document.getElementById('totalCustomerCount').textContent = total;
        }

        function renderPagination(pageCount) {
            const container = document.getElementById('totalVisibleCount').parentElement.nextElementSibling;
            const buttonClass = 'w-7 h-7 rounded-lg border border-border text-secondary hover:bg-gray-50 flex items-center justify-center';
            const activeClass = 'w-7 h-7 rounded-lg bg-primary text-white font-medium flex items-center justify-center';
            const disabledClass = 'opacity-40 cursor-not-allowed';
            let buttons = '<button type="button" data-customer-page="' + (customerPage - 1) + '" aria-label="Halaman sebelumnya" class="' + buttonClass + (customerPage <= 1 ? ' ' + disabledClass : '') + '"' + (customerPage <= 1 ? ' disabled' : '') + '><i data-lucide="chevron-left" class="w-3.5 h-3.5"></i></button>';
            let pages = [];
            if (pageCount <= 7) {
                pages = Array.from({ length: pageCount }, (_, index) => index + 1);
            } else if (customerPage <= 4) {
                pages = [1, 2, 3, 4, 5, '...', pageCount];
            } else if (customerPage >= pageCount - 3) {
                pages = [1, '...', pageCount - 4, pageCount - 3, pageCount - 2, pageCount - 1, pageCount];
            } else {
                pages = [1, '...', customerPage - 1, customerPage, customerPage + 1, '...', pageCount];
            }
            pages.forEach(page => {
                if (page === '...') {
                    buttons += '<span class="px-1 text-secondary" aria-hidden="true">...</span>';
                    return;
                }
                buttons += '<button type="button" data-customer-page="' + page + '" class="' + (page === customerPage ? activeClass : buttonClass) + '"' + (page === customerPage ? ' aria-current="page"' : '') + '>' + page + '</button>';
            });
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
            renderTable(getCurrentFilteredData());
        }

        function getCurrentFilteredData() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            const statusVal = document.getElementById('statusFilter').value;
            const areaVal = document.getElementById('areaFilter').value;
            const monthVal = document.getElementById('monthFilter').value;
            const yearVal = document.getElementById('yearFilter').value;
            const dateVal = document.getElementById('dateFilter').value;

            return customersData.filter(cust => {
                const matchQuery = cust.name.toLowerCase().includes(query) ||
                                       String(cust.reporter_name || '').toLowerCase().includes(query) ||
                                       String(cust.reporter_contact || '').toLowerCase().includes(query) ||
                                       cust.custNo.toLowerCase().includes(query) ||
                                       cust.address.toLowerCase().includes(query) ||
                                       cust.email.toLowerCase().includes(query);
                const matchStatus = statusVal === "" || cust.status === statusVal;
                const matchArea = areaVal === "" || cust.area === areaVal;
                const registrationDate = cust.registration_date || '';
                const matchMonth = monthVal === '' || registrationDate.slice(5, 7) === monthVal;
                const matchYear = yearVal === '' || registrationDate.slice(0, 4) === yearVal;
                const matchDate = dateVal === '' || registrationDate === dateVal;
                return matchQuery && matchStatus && matchArea && matchMonth && matchYear && matchDate;
            });
        }

        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('areaFilter').value = '';
            document.getElementById('monthFilter').value = '';
            document.getElementById('yearFilter').value = '';
            document.getElementById('dateFilter').value = '';
            customerPage = 1;
            renderTable(customersData);
        }

        function toggleSelectAll(e) {
            const visibleCustomers = getCurrentFilteredData().slice((customerPage - 1) * customersPerPage, customerPage * customersPerPage);
            visibleCustomers.forEach(customer => {
                const id = Number(customer.id);
                if (e.target.checked) selectedCustomerIds.add(id);
                else selectedCustomerIds.delete(id);
            });
            document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = e.target.checked);
            updateSelectionControls(getCurrentFilteredData());
        }

        function updateSelectionControls(filteredCustomers) {
            const currentPageCustomers = filteredCustomers.slice((customerPage - 1) * customersPerPage, customerPage * customersPerPage);
            const selectedOnPage = currentPageCustomers.filter(customer => selectedCustomerIds.has(Number(customer.id))).length;
            const selectAll = document.getElementById('selectAll');
            selectAll.checked = currentPageCustomers.length > 0 && selectedOnPage === currentPageCustomers.length;
            selectAll.indeterminate = selectedOnPage > 0 && selectedOnPage < currentPageCustomers.length;

            const deleteButton = document.getElementById('deleteSelectedCustomersBtn');
            const selectedCount = selectedCustomerIds.size;
            deleteButton.disabled = selectedCount === 0;
            document.getElementById('deleteSelectedCustomersLabel').textContent = selectedCount ? `Hapus (${selectedCount})` : 'Hapus';
        }

        function showCustomerToast(message) {
            const toast = document.getElementById('customer-toast');
            toast.textContent = message;
            toast.classList.remove('hidden');
            clearTimeout(customerToastTimer);
            customerToastTimer = window.setTimeout(() => toast.classList.add('hidden'), 2800);
        }

        async function deleteSelectedCustomers() {
            const ids = [...selectedCustomerIds];
            if (!ids.length) return;
            const confirmation = `Apakah Anda yakin ingin menghapus ${ids.length} data yang dipilih?`;
            if (!window.confirm(confirmation)) return;

            try {
                const result = await requestJson(@json(route('customers.bulk-destroy')), 'DELETE', { ids });
                ids.forEach(id => selectedCustomerIds.delete(id));
                for (let index = customersData.length - 1; index >= 0; index--) {
                    if (ids.includes(Number(customersData[index].id))) customersData.splice(index, 1);
                }

                if (!currentSelectedCustomer || ids.includes(Number(currentSelectedCustomer.id))) {
                    currentSelectedCustomer = customersData[0] || null;
                    loadCustomerDetail(currentSelectedCustomer);
                    if (currentSelectedCustomer) loadCustomerRecords(currentSelectedCustomer);
                }

                document.getElementById('totalCustomerStat').textContent = customersData.length.toLocaleString('id-ID');
                document.getElementById('activeCustomerStat').textContent = customersData.filter(customer => customer.status === 'Aktif').length.toLocaleString('id-ID');
                document.getElementById('newCustomerStat').textContent = customersData.filter(customer => customer.status === 'Baru').length.toLocaleString('id-ID');
                document.getElementById('inactiveCustomerStat').textContent = customersData.filter(customer => customer.status === 'Nonaktif').length.toLocaleString('id-ID');
                document.getElementById('totalCustomerCount').textContent = customersData.length;
                customerPage = Math.min(customerPage, Math.max(1, Math.ceil(getCurrentFilteredData().length / customersPerPage)));
                renderTable(getCurrentFilteredData());
                showCustomerToast(result.message);
            } catch (error) {
                window.alert(error.message);
            }
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
        const customerNameInput = document.getElementById('customer-name');
        const customerNamePicker = document.getElementById('customer-name-picker');
        const customerNameLabel = document.getElementById('customer-name-label');
        const customerNameSearch = document.getElementById('customer-opd-search');
        const customerNameDropdown = document.getElementById('customer-opd-dropdown');
        const customerOpdOptions = @json($opdOptions);
        const customerCodes = @json($customerCodes);
        const googleMapsApiKey = @json(config('services.google_maps.browser_key'));
        const customerAreaSearch = document.getElementById('customer-area-search');
        const customerAreaOptions = document.getElementById('customer-area-options');
        let customerMap = null;
        let customerMarker = null;
        let customerGeocoder = null;
        let googleMapsPromise = null;
        let areaSearchTimer = null;
        let areaSearchRequest = 0;
        let opdLocationRequest = 0;
        let nominatimQueue = Promise.resolve();
        let lastNominatimRequestAt = 0;
        const opdLocationCache = new Map();
        let locationLookupRequest = 0;
        let isApplyingLocation = false;
        let originalEditCode = null;

        function setCustomerLocationStatus(message, isError = false, isLoading = false) {
            const status = document.getElementById('customer-location-status');
            status.textContent = message;
            status.className = `mt-1 min-h-4 text-[11px] ${isError ? 'text-red-600' : 'text-secondary'}`;
            status.setAttribute('aria-busy', String(isLoading));
        }

        function getGeocodingErrorMessage(error, fallback) {
            const code = String(error?.code || error?.message || '').toUpperCase();
            if (code.includes('REQUEST_DENIED')) {
                return 'Google menolak permintaan geocoding. Periksa Geocoding API, billing, dan batasan key/referrer di Google Cloud.';
            }
            if (code.includes('OVER_QUERY_LIMIT')) {
                return 'Batas permintaan alamat Google Maps tercapai. Tunggu sebentar lalu coba lagi.';
            }
            if (code.includes('ZERO_RESULTS')) {
                return 'Alamat untuk titik yang dipilih tidak ditemukan.';
            }
            if (code.includes('UNKNOWN_ERROR') || code.includes('ERROR')) {
                return 'Google Maps gagal mencari alamat sementara. Silakan coba lagi.';
            }
            return error?.message || fallback;
        }

        function setCustomerLocationField(id, value) {
            const field = document.getElementById(id);
            field.value = value;
            field.dispatchEvent(new Event('input', { bubbles: true }));
            field.dispatchEvent(new Event('change', { bubbles: true }));
        }

        function searchNominatim(url) {
            const request = nominatimQueue.then(async () => {
                const wait = Math.max(0, 1100 - (Date.now() - lastNominatimRequestAt));
                if (wait) await new Promise(resolve => window.setTimeout(resolve, wait));
                lastNominatimRequestAt = Date.now();
                const response = await fetch(url, { headers: { Accept: 'application/json' } });
                if (!response.ok) throw new Error('Layanan pencarian lokasi tidak dapat diakses.');
                return response.json();
            });
            nominatimQueue = request.then(() => undefined, () => undefined);
            return request;
        }

        function populateAreaFilter() {
            const filter = document.getElementById('areaFilter');
            const existingAreas = new Set(Array.from(filter.options, option => option.value));
            [...new Set(customersData.map(customer => customer.area).filter(Boolean))]
                .sort((a, b) => a.localeCompare(b, 'id'))
                .forEach(area => {
                    if (!existingAreas.has(area)) filter.add(new Option(area, area));
                });
        }

        function setCustomerCoordinates(latitude, longitude) {
            setCustomerLocationField('customer-latitude', Number(latitude).toFixed(7));
            setCustomerLocationField('customer-longitude', Number(longitude).toFixed(7));
            updateCustomerMap(latitude, longitude);
        }

        function setGoogleMapsFallback(latitude = null, longitude = null, search = '') {
            const container = document.getElementById('customer-location-map');
            const query = latitude !== null && longitude !== null
                ? `${Number(latitude)},${Number(longitude)}`
                : (search || 'Situbondo, Jawa Timur, Indonesia');
            const url = new URL('https://maps.google.com/maps');
            url.search = new URLSearchParams({
                q: query,
                t: '',
                z: latitude !== null && longitude !== null ? '17' : '13',
                ie: 'UTF8',
                iwloc: '',
                output: 'embed',
            });
            let frame = document.getElementById('customer-location-map-frame');
            if (!frame) {
                frame = document.createElement('iframe');
                frame.id = 'customer-location-map-frame';
                frame.title = 'Google Maps Kabupaten Situbondo';
                frame.className = 'h-full w-full border-0';
                frame.allowFullscreen = true;
                frame.loading = 'lazy';
                frame.referrerPolicy = 'no-referrer-when-downgrade';
                container.replaceChildren(frame);
            }
            if (frame.src !== url.toString()) frame.src = url.toString();
        }

        function updateCustomerMap(latitude = null, longitude = null, search = '') {
            const query = latitude !== null && longitude !== null
                ? `${Number(latitude)},${Number(longitude)}`
                : (search || 'Situbondo, Jawa Timur, Indonesia');
            if (!customerMap || !window.google?.maps) {
                setGoogleMapsFallback(latitude, longitude, search);
                return;
            }
            if (latitude !== null && longitude !== null) {
                const point = { lat: Number(latitude), lng: Number(longitude) };
                customerMap.setCenter(point);
                customerMap.setZoom(17);
                if (!customerMarker) {
                    customerMarker = new google.maps.Marker({ map: customerMap, position: point, draggable: true });
                    customerMarker.addListener('dragend', event => selectCustomerLocation(event.latLng));
                } else {
                    customerMarker.setMap(customerMap);
                    customerMarker.setPosition(point);
                }
            } else if (search) {
                customerGeocoder?.geocode({ address: search, region: 'id' })
                    .then(({ results }) => {
                        const result = results.find(item => isInsideSitubondo(
                            item.geometry.location.lat(),
                            item.geometry.location.lng(),
                            item,
                        ));
                        if (!result) return;
                        customerMap.setCenter(result.geometry.location);
                        customerMap.setZoom(15);
                    })
                    .catch(error => setCustomerLocationStatus(
                        getGeocodingErrorMessage(error, 'Pencarian Google Maps gagal.'),
                        true,
                    ));
            }
        }

        function loadGoogleMaps() {
            if (window.google?.maps) return Promise.resolve();
            if (!googleMapsApiKey || /key_asli|key_dari_google|your[_ -]?key|placeholder|\.\.\./i.test(googleMapsApiKey)) {
                return Promise.reject(new Error('Untuk memilih titik langsung, isi GOOGLE_MAPS_API_KEY dengan key asli dan aktifkan Maps JavaScript API serta Geocoding API. Peta sematan tetap dapat digunakan untuk melihat lokasi.'));
            }
            if (googleMapsPromise) return googleMapsPromise;

            googleMapsPromise = new Promise((resolve, reject) => {
                const timeout = window.setTimeout(() => {
                    googleMapsPromise = null;
                    reject(new Error('Google Maps terlalu lama dimuat. Periksa key, API, billing, dan HTTP referrer di Google Cloud.'));
                }, 15000);
                window.initCustomerGoogleMaps = () => {
                    window.clearTimeout(timeout);
                    resolve();
                };
                window.gm_authFailure = () => {
                    window.clearTimeout(timeout);
                    googleMapsPromise = null;
                    reject(new Error('Google menolak key. Periksa Maps JavaScript API, Geocoding API, billing, dan HTTP referrer.'));
                };
                const script = document.createElement('script');
                script.src = `https://maps.googleapis.com/maps/api/js?key=${encodeURIComponent(googleMapsApiKey)}&callback=initCustomerGoogleMaps&v=weekly`;
                script.async = true;
                script.defer = true;
                script.onerror = () => {
                    window.clearTimeout(timeout);
                    googleMapsPromise = null;
                    reject(new Error('Google Maps gagal dimuat. Periksa koneksi internet dan konfigurasi key.'));
                };
                document.head.appendChild(script);
            });
            return googleMapsPromise;
        }

        function initializeCustomerMap() {
            const latitude = document.getElementById('customer-latitude').value;
            const longitude = document.getElementById('customer-longitude').value;
            if (customerMap) {
                google.maps.event.trigger(customerMap, 'resize');
                if (latitude && longitude) updateCustomerMap(latitude, longitude);
                return;
            }

            loadGoogleMaps().then(() => {
                const container = document.getElementById('customer-location-map');
                customerMap = new google.maps.Map(container, {
                    center: { lat: -7.706, lng: 114.009 },
                    zoom: 10,
                    restriction: {
                        latLngBounds: new google.maps.LatLngBounds(
                            { lat: -8.05, lng: 113.5 },
                            { lat: -7.3, lng: 114.65 },
                        ),
                        strictBounds: true,
                    },
                    mapTypeControl: true,
                    streetViewControl: false,
                    fullscreenControl: true,
                });
                customerGeocoder = new google.maps.Geocoder();
                customerMap.addListener('click', event => selectCustomerLocation(event.latLng));
                if (latitude && longitude) updateCustomerMap(latitude, longitude);
                setCustomerLocationStatus('Peta Google Maps interaktif siap. Klik titik lokasi untuk mengisi alamat dan area.');
            }).catch(error => {
                setGoogleMapsFallback(latitude || null, longitude || null);
                setCustomerLocationStatus(error.message, true);
            });
        }

        function showCustomerMapPoint(latitude, longitude) {
            updateCustomerMap(latitude, longitude);
        }

        function applyCustomerLocation(location) {
            locationLookupRequest++;
            isApplyingLocation = true;
            const latitude = Number(location.latitude ?? location.lat);
            const longitude = Number(location.longitude ?? location.lon ?? location.lng);
            if (!Number.isFinite(latitude) || !Number.isFinite(longitude) ||
                latitude < -8.05 || latitude > -7.3 || longitude < 113.5 || longitude > 114.65) {
                isApplyingLocation = false;
                setCustomerLocationStatus('Lokasi tidak berada di wilayah Kabupaten Situbondo.', true);
                return;
            }

            const area = location.area || location.address?.village || location.address?.suburb ||
                location.address?.city_district || location.address?.town || location.address?.municipality ||
                location.address?.city || location.address?.county || 'Situbondo';
            const address = location.address_text || location.display_name || location.address || '';
            setCustomerLocationField('customer-area', area);
            setCustomerLocationField('customer-address', typeof address === 'string' ? address : '');
            customerAreaSearch.value = location.name ? `${area} - ${location.name}` : area;
            setCustomerCoordinates(latitude, longitude);
            isApplyingLocation = false;
            showCustomerMapPoint(latitude, longitude);
            customerAreaOptions.classList.add('hidden');
            setCustomerLocationStatus('Area dan titik lokasi berhasil dipilih.');
        }

        function getGoogleAddressArea(result) {
            const components = result.address_components || [];
            const areaTypes = [
                'administrative_area_level_4',
                'sublocality_level_1',
                'administrative_area_level_3',
                'locality',
                'administrative_area_level_2',
            ];
            for (const type of areaTypes) {
                const component = components.find(item => item.types.includes(type));
                if (component) return component.long_name;
            }
            return 'Situbondo';
        }

        function isInsideSitubondo(latitude, longitude, result = null) {
            const insideBounds = latitude >= -8.05 && latitude <= -7.3 &&
                longitude >= 113.5 && longitude <= 114.65;
            const address = result?.formatted_address?.toLocaleLowerCase('id') || '';
            return insideBounds && (!address || address.includes('situbondo'));
        }

        async function reverseGeocodeOpenStreetMap(latitude, longitude) {
            const url = new URL('https://nominatim.openstreetmap.org/reverse');
            url.search = new URLSearchParams({
                format: 'jsonv2',
                lat: String(latitude),
                lon: String(longitude),
                zoom: '18',
                addressdetails: '1',
                'accept-language': 'id',
            });
            const result = await searchNominatim(url);
            if (!result.display_name) throw new Error('Alamat untuk titik ini tidak ditemukan.');
            return {
                area: result.address?.village || result.address?.suburb ||
                    result.address?.city_district || result.address?.town ||
                    result.address?.municipality || result.address?.city ||
                    result.address?.county || 'Situbondo',
                address: result.display_name,
            };
        }

        async function selectCustomerLocation(point) {
            const latitude = typeof point.lat === 'function' ? point.lat() : Number(point.lat);
            const longitude = typeof point.lng === 'function' ? point.lng() : Number(point.lng);
            if (latitude < -8.05 || latitude > -7.3 || longitude < 113.5 || longitude > 114.65) {
                setCustomerLocationStatus('Pilih titik di wilayah Kabupaten Situbondo.', true);
                return;
            }

            const requestId = ++locationLookupRequest;
            isApplyingLocation = true;
            setCustomerCoordinates(latitude, longitude);
            setCustomerLocationField('customer-area', '');
            setCustomerLocationField('customer-address', '');
            isApplyingLocation = false;
            setCustomerLocationStatus('Mengambil alamat dan area dari titik yang dipilih...', false, true);
            try {
                let area;
                let address;
                let usedFallback = false;
                try {
                    const { results } = await customerGeocoder.geocode({
                        location: { lat: latitude, lng: longitude },
                        region: 'id',
                    });
                    const result = results.find(item => isInsideSitubondo(latitude, longitude, item));
                    if (!result) throw new Error('Google tidak menemukan alamat untuk titik ini.');
                    area = getGoogleAddressArea(result);
                    address = result.formatted_address;
                } catch {
                    const fallback = await reverseGeocodeOpenStreetMap(latitude, longitude);
                    area = fallback.area;
                    address = fallback.address;
                    usedFallback = true;
                }
                if (requestId !== locationLookupRequest) return;

                isApplyingLocation = true;
                setCustomerLocationField('customer-area', area);
                setCustomerLocationField('customer-address', address);
                customerAreaSearch.value = document.getElementById('customer-area').value;
                isApplyingLocation = false;
                setCustomerLocationStatus(usedFallback
                    ? 'Area, alamat, dan koordinat terisi. Geocoding Google tidak tersedia; alamat diambil dari layanan lokasi alternatif.'
                    : 'Alamat lengkap dan area berhasil diisi dari Google Maps.');
            } catch (error) {
                if (requestId === locationLookupRequest) {
                    isApplyingLocation = true;
                    setCustomerLocationField('customer-area', '');
                    setCustomerLocationField('customer-address', '');
                    isApplyingLocation = false;
                    setCustomerLocationStatus(
                        getGeocodingErrorMessage(error, 'Alamat titik tidak dapat ditemukan.'),
                        true,
                    );
                }
            }
        }

        function resetCustomerMap() {
            if (customerMap && customerMarker) customerMarker.setMap(null);
            if (customerMap) {
                customerMap = null;
                customerGeocoder = null;
            }
            setGoogleMapsFallback();
        }

        function renderOpdOptions() {
            const query = customerNameSearch.value.trim().toLocaleLowerCase('id');
            const matches = customerOpdOptions.filter(option => option.name.toLocaleLowerCase('id').includes(query));
            const container = document.getElementById('customer-opd-options');
            container.replaceChildren();

            if (!matches.length) {
                const empty = document.createElement('p');
                empty.className = 'px-3 py-2 text-xs text-secondary';
                empty.textContent = 'OPD tidak ditemukan.';
                container.appendChild(empty);
                return;
            }

            matches.forEach(option => {
                const button = document.createElement('button');
                button.type = 'button';
                button.setAttribute('role', 'option');
                button.className = 'block w-full rounded-lg px-3 py-2 text-left text-xs text-dark hover:bg-blue-50';
                button.textContent = option.name;
                button.addEventListener('click', () => selectOpd(option));
                container.appendChild(button);
            });
        }

        function setOpdDropdownOpen(open) {
            customerNameDropdown.classList.toggle('hidden', !open);
            customerNamePicker.setAttribute('aria-expanded', String(open));
            if (open) {
                renderOpdOptions();
                customerNameSearch.focus();
            }
        }

        function selectOpd(opd) {
            const requestId = ++opdLocationRequest;
            locationLookupRequest++;
            customerNameInput.value = opd.name;
            customerNameLabel.textContent = opd.name;
            setOpdDropdownOpen(false);
            updateCustomerCodePreview();
            if (opd.latitude && opd.longitude) {
                applyCustomerLocation({
                    ...opd,
                    address_text: opd.address || '',
                });
                return;
            }

            if (opd.area || opd.address) {
                isApplyingLocation = true;
                setCustomerLocationField('customer-area', opd.area || '');
                setCustomerLocationField('customer-address', opd.address || '');
                isApplyingLocation = false;
            }

            const cachedLocation = opdLocationCache.get(opd.name);
            if (cachedLocation) {
                applyCustomerLocation(cachedLocation);
                return;
            }

            isApplyingLocation = true;
            setCustomerLocationField('customer-area', opd.area || '');
            setCustomerLocationField('customer-address', opd.address || '');
            setCustomerLocationField('customer-latitude', '');
            setCustomerLocationField('customer-longitude', '');
            isApplyingLocation = false;
            if (customerMarker) customerMarker.setMap(null);
            customerAreaSearch.value = opd.name;

            lookupOpdLocation(opd, requestId);
        }

        function parseOpdSearchResults(results) {
            return results
                .filter(result => {
                    const latitude = Number(result.lat);
                    const longitude = Number(result.lon);
                    return latitude >= -8.05 && latitude <= -7.3 &&
                        longitude >= 113.5 && longitude <= 114.65;
                })
                .map(result => ({
                    name: result.name,
                    area: result.address?.village || result.address?.suburb ||
                        result.address?.city_district || result.address?.town ||
                        result.address?.municipality || result.address?.city ||
                        result.address?.county || 'Situbondo',
                    latitude: result.lat,
                    longitude: result.lon,
                    address: result.address,
                    address_text: result.display_name,
                    display_name: result.display_name,
                    label: result.display_name,
                    importance: result.importance || 0,
                    category: result.category || '',
                    type: result.type || '',
                }))
                .sort((a, b) => {
                    const aOffice = /office|government|civic|amenity/i.test(`${a.category} ${a.type}`) ? 1 : 0;
                    const bOffice = /office|government|civic|amenity/i.test(`${b.category} ${b.type}`) ? 1 : 0;
                    return bOffice - aOffice || b.importance - a.importance;
                });
        }

        async function lookupOpdLocation(opd, requestId) {
            setCustomerLocationStatus(`Mencari titik lokasi ${opd.name} di Situbondo...`, false, true);
            customerAreaSearch.value = opd.name;
            if (customerMap) {
                customerMap.setCenter({ lat: -7.706, lng: 114.009 });
                customerMap.setZoom(10);
            }
            updateCustomerMap(null, null, `${opd.name}, Kabupaten Situbondo, Jawa Timur, Indonesia`);

            const queries = [
                `${opd.name}, Kabupaten Situbondo, Jawa Timur, Indonesia`,
                `Kantor ${opd.name}, Kabupaten Situbondo, Jawa Timur, Indonesia`,
                `${opd.name.replace(/^(Dinas|Badan|Kantor|Sekretariat)\\s+/i, '')}, Situbondo, Jawa Timur, Indonesia`,
            ];
            let locations = [];

            try {
                for (const query of queries) {
                    const url = new URL('https://nominatim.openstreetmap.org/search');
                    url.search = new URLSearchParams({
                        format: 'jsonv2',
                        q: query,
                        viewbox: '113.5,-7.3,114.65,-8.05',
                        bounded: '1',
                        addressdetails: '1',
                        limit: '8',
                        countrycodes: 'id',
                        'accept-language': 'id',
                    });
                    locations = parseOpdSearchResults(await searchNominatim(url));
                    if (locations.length) break;
                }

                if (requestId !== opdLocationRequest) return;
                const exactOffice = locations.find(location =>
                    /office|government|civic/i.test(`${location.category} ${location.type}`));
                const selectedLocation = exactOffice || locations[0];
                if (selectedLocation) {
                    opdLocationCache.set(opd.name, selectedLocation);
                    applyCustomerLocation(selectedLocation);
                    return;
                }

                renderAreaOptions([], `Titik khusus ${opd.name} belum ditemukan di data peta. Klik lokasi kantornya langsung pada peta atau cari alamat lengkap di kolom ini.`);
                setCustomerLocationStatus(
                    `Titik khusus ${opd.name} belum terdaftar pada data peta. Peta dipusatkan ke Situbondo; klik lokasi kantor di peta untuk mengisi koordinat, area, dan alamat otomatis.`,
                    true,
                );
            } catch (error) {
                if (requestId !== opdLocationRequest) return;
                renderAreaOptions([], `${error.message} Coba ulangi pencarian area/alamat.`);
                setCustomerLocationStatus(
                    `Lokasi ${opd.name} gagal dicari: ${error.message}`,
                    true,
                );
            }
        }

        function previewCustomerCode() {
            const name = customerNameInput.value || document.getElementById('customer-name-display').value;
            if (!name) return '';

            if (customerMethodInput.disabled === false && originalEditCode) {
                return originalEditCode;
            }

            const lastSequence = customerCodes.reduce((max, code) => {
                const match = String(code).match(/^OPD(\d+)$/);
                return match ? Math.max(max, Number(match[1])) : max;
            }, 0);
            return `OPD${String(lastSequence + 1).padStart(4, '0')}`;
        }

        function updateCustomerCodePreview() {
            document.getElementById('customer-code-display').textContent =
                previewCustomerCode() || 'Kode otomatis muncul setelah nama OPD dan tanggal dipilih';
        }

        function searchSavedLocations(query) {
            const normalizedQuery = query.toLocaleLowerCase('id');
            return customersData
                .filter(customer => customer.latitude && customer.longitude &&
                    `${customer.name} ${customer.area || ''} ${customer.address || ''}`.toLocaleLowerCase('id').includes(normalizedQuery))
                .slice(0, 5)
                .map(customer => ({
                    name: customer.name,
                    area: customer.area,
                    address_text: customer.address,
                    latitude: customer.latitude,
                    longitude: customer.longitude,
                    label: `${customer.area || 'Situbondo'} - ${customer.name}`,
                }));
        }

        function renderAreaOptions(locations, message = '') {
            customerAreaOptions.replaceChildren();
            if (!locations.length && message) {
                const empty = document.createElement('p');
                empty.className = 'px-3 py-2 text-xs text-secondary';
                empty.textContent = message;
                customerAreaOptions.appendChild(empty);
            }

            locations.forEach(location => {
                const button = document.createElement('button');
                button.type = 'button';
                button.setAttribute('role', 'option');
                button.className = 'block w-full rounded-lg px-3 py-2 text-left text-xs text-dark hover:bg-blue-50';
                button.textContent = location.label || location.display_name;
                button.addEventListener('click', () => applyCustomerLocation(location));
                customerAreaOptions.appendChild(button);
            });

            customerAreaOptions.classList.toggle('hidden', !locations.length && !message);
        }

        function searchCustomerArea(query) {
            const requestId = ++areaSearchRequest;
            const savedLocations = searchSavedLocations(query);
            updateCustomerMap(null, null, `${query}, Kabupaten Situbondo, Jawa Timur, Indonesia`);
            renderAreaOptions(savedLocations, 'Mencari lokasi di Situbondo...');

            const url = new URL('https://nominatim.openstreetmap.org/search');
            url.search = new URLSearchParams({
                format: 'jsonv2',
                q: `${query}, Kabupaten Situbondo, Jawa Timur, Indonesia`,
                viewbox: '113.5,-7.3,114.65,-8.05',
                bounded: '1',
                addressdetails: '1',
                limit: '8',
                countrycodes: 'id',
                'accept-language': 'id',
            });

            searchNominatim(url)
                .then(results => {
                    if (requestId !== areaSearchRequest) return;
                    const geocodedLocations = parseOpdSearchResults(results);
                    const knownCoordinates = new Set(
                        savedLocations.map(location => `${location.latitude},${location.longitude}`),
                    );
                    const locations = [
                        ...savedLocations,
                        ...geocodedLocations.filter(location =>
                            !knownCoordinates.has(`${location.latitude},${location.longitude}`)),
                    ];
                    renderAreaOptions(
                        locations,
                        locations.length ? '' : 'Lokasi tidak ditemukan. Coba nama jalan, desa, kecamatan, atau OPD.',
                    );
                })
                .catch(error => {
                    if (requestId !== areaSearchRequest) return;
                    renderAreaOptions(
                        savedLocations,
                        savedLocations.length ? '' : `${error.message} Periksa koneksi internet atau coba lagi.`,
                    );
                });
        }

        async function lookupAddressFromCoordinates(latitude, longitude) {
            const requestId = ++locationLookupRequest;
            if (latitude < -8.05 || latitude > -7.3 || longitude < 113.5 || longitude > 114.65) {
                setCustomerLocationStatus('Koordinat harus berada di wilayah Kabupaten Situbondo.', true);
                return;
            }

            updateCustomerMap(latitude, longitude);
            setCustomerLocationStatus('Mencari alamat dan area dari koordinat...', false, true);
            if (customerGeocoder) {
                try {
                    const { results } = await customerGeocoder.geocode({
                        location: { lat: latitude, lng: longitude },
                        region: 'id',
                    });
                    if (requestId !== locationLookupRequest) return;
                    const result = results.find(item => isInsideSitubondo(latitude, longitude, item));
                    if (!result) throw new Error('Alamat untuk koordinat ini tidak ditemukan di Situbondo.');
                    isApplyingLocation = true;
                    setCustomerLocationField('customer-area', getGoogleAddressArea(result));
                    setCustomerLocationField('customer-address', result.formatted_address);
                    customerAreaSearch.value = document.getElementById('customer-area').value;
                    isApplyingLocation = false;
                    setCustomerLocationStatus('Alamat lengkap dan area berhasil diisi dari Google Maps.');
                } catch (error) {
                    if (requestId === locationLookupRequest) {
                        isApplyingLocation = false;
                        setCustomerLocationStatus(
                            getGeocodingErrorMessage(error, 'Pencarian alamat Google gagal.'),
                            true,
                        );
                    }
                }
                return;
            }

            const url = new URL('https://nominatim.openstreetmap.org/reverse');
            url.search = new URLSearchParams({
                format: 'jsonv2',
                lat: String(latitude),
                lon: String(longitude),
                zoom: '18',
                addressdetails: '1',
                'accept-language': 'id',
            });

            try {
                const result = await searchNominatim(url);
                if (requestId !== locationLookupRequest) return;
                if (!result.display_name) throw new Error('Alamat tidak ditemukan untuk koordinat tersebut.');

                const area = result.address?.city_district || result.address?.village ||
                    result.address?.town || result.address?.suburb ||
                    result.address?.municipality || result.address?.city ||
                    result.address?.county || 'Situbondo';
                isApplyingLocation = true;
                setCustomerLocationField('customer-area', area);
                setCustomerLocationField('customer-address', result.display_name);
                customerAreaSearch.value = area;
                isApplyingLocation = false;
                setCustomerLocationStatus('Alamat lengkap dan area berhasil diisi dari koordinat.');
            } catch (error) {
                if (requestId === locationLookupRequest) {
                    isApplyingLocation = false;
                    setCustomerLocationStatus(`${error.message} Isi area dan alamat secara manual bila perlu.`, true);
                }
            }
        }

        function localDateInputValue() {
            const now = new Date();
            return `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
        }

        function setCustomerFieldLocked(id, locked) {
            const field = document.getElementById(id);
            field.disabled = locked;
            field.classList.toggle('bg-gray-100', locked);
            field.classList.toggle('text-gray-500', locked);
        }

        function setCustomerPackageSelection(packageName, allowLegacyPackage = false) {
            const packageSelect = document.getElementById('customer-package');
            packageSelect.querySelectorAll('option[data-legacy-package]').forEach(option => option.remove());

            const standardPackages = [
                '50 Mbps',
                '100 Mbps',
                '200 Mbps',
                '300 Mbps',
                '500 Mbps',
                '750 Mbps',
                '1 Gbps',
            ];
            const isStandardPackage = standardPackages.includes(packageName);

            if (allowLegacyPackage && packageName && !isStandardPackage) {
                const legacyOption = new Option(`${packageName} (tersimpan)`, packageName);
                legacyOption.dataset.legacyPackage = 'true';
                packageSelect.add(legacyOption);
            }

            packageSelect.value = isStandardPackage || allowLegacyPackage ? packageName : '';
        }

        function openAddModal() {
            customerForm.reset();
            setCustomerPackageSelection('');
            customerForm.action = @json(route('customers.store'));
            customerMethodInput.disabled = true;
            document.getElementById('customer-form-id').value = '';
            document.getElementById('customer-form-title').innerText = 'Tambah OPD';
            document.getElementById('customer-form-submit').innerText = 'Simpan OPD';
            document.getElementById('customer-opd-selector').classList.remove('hidden');
            document.getElementById('customer-name-locked').classList.add('hidden');
            customerNameInput.value = '';
            customerNameLabel.textContent = 'Pilih OPD dari data OpdSeeder';
            customerNameSearch.value = '';
            setOpdDropdownOpen(false);
            customerAreaSearch.value = '';
            customerAreaOptions.classList.add('hidden');
            areaSearchRequest++;
            document.getElementById('customer-registration-date').value = localDateInputValue();
            originalEditCode = null;
            ['customer-reporter-name', 'customer-reporter-contact'].forEach(id => setCustomerFieldLocked(id, false));
            document.getElementById('customer-latitude').value = '';
            document.getElementById('customer-longitude').value = '';
            document.getElementById('customer-area').value = '';
            document.getElementById('customer-address').value = '';
            resetCustomerMap();
            setCustomerLocationStatus('Cari lokasi di Google Maps, pilih OPD tersimpan untuk koordinat otomatis, atau isi koordinat secara manual.');
            updateCustomerCodePreview();
            document.getElementById('modal-add-customer').classList.remove('hidden');
            window.setTimeout(initializeCustomerMap, 100);
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
            document.getElementById('customer-opd-selector').classList.add('hidden');
            document.getElementById('customer-name-locked').classList.remove('hidden');
            document.getElementById('customer-name-display').value = customer.name;
            customerNameInput.value = customer.name;
            document.getElementById('customer-registration-date').value = customer.registration_date || '';
            originalEditCode = customer.custNo || '';
            document.getElementById('customer-reporter-name').value = blank(customer.reporter_name);
            document.getElementById('customer-reporter-contact').value = blank(customer.reporter_contact);
            ['customer-reporter-name', 'customer-reporter-contact'].forEach(id => setCustomerFieldLocked(id, true));
            updateCustomerCodePreview();
            document.getElementById('customer-area').value = blank(customer.area);
            customerAreaSearch.value = blank(customer.area);
            customerAreaOptions.classList.add('hidden');
            document.getElementById('customer-address').value = blank(customer.address);
            const packageName = blank(customer.package_name);
            setCustomerPackageSelection(packageName, true);
            document.getElementById('customer-status').value = customer.status;
            document.getElementById('customer-latitude').value = customer.latitude ?? '';
            document.getElementById('customer-longitude').value = customer.longitude ?? '';
            resetCustomerMap();
            setCustomerLocationStatus(customer.latitude && customer.longitude
                ? 'Titik lokasi tersimpan. Peta menampilkan koordinat OPD.'
                : 'Cari lokasi di Google Maps dan isi koordinat kantor OPD.');
            document.getElementById('modal-add-customer').classList.remove('hidden');
            window.setTimeout(() => {
                initializeCustomerMap();
                if (customer.latitude && customer.longitude) {
                    showCustomerMapPoint(customer.latitude, customer.longitude);
                }
            }, 100);
        }

        function closeAddModal() { document.getElementById('modal-add-customer').classList.add('hidden'); }
        document.getElementById('btn-open-add-modal').addEventListener('click', openAddModal);
        customerNamePicker.addEventListener('click', () => setOpdDropdownOpen(customerNameDropdown.classList.contains('hidden')));
        customerNameSearch.addEventListener('input', renderOpdOptions);
        customerAreaSearch.addEventListener('input', () => {
            const query = customerAreaSearch.value.trim();
            window.clearTimeout(areaSearchTimer);
            areaSearchRequest++;
            locationLookupRequest++;
            if (query.length < 2) {
                document.getElementById('customer-area').value = '';
                document.getElementById('customer-address').value = '';
                document.getElementById('customer-latitude').value = '';
                document.getElementById('customer-longitude').value = '';
                customerAreaOptions.classList.add('hidden');
                return;
            }
            document.getElementById('customer-area').value = '';
            document.getElementById('customer-address').value = '';
            document.getElementById('customer-latitude').value = '';
            document.getElementById('customer-longitude').value = '';
            setCustomerLocationStatus('Pilih hasil pencarian untuk mengisi area dan titik lokasi.');
            areaSearchTimer = window.setTimeout(() => searchCustomerArea(query), 1000);
        });
        document.getElementById('customer-registration-date').addEventListener('change', updateCustomerCodePreview);
        document.addEventListener('click', event => {
            if (!event.target.closest('#customer-opd-selector')) setOpdDropdownOpen(false);
            if (!event.target.closest('#customer-area-search') && !event.target.closest('#customer-area-options')) {
                customerAreaOptions.classList.add('hidden');
            }
        });
        @if ($errors->any())
            const failedCustomerId = @json(old('_customer_id'));
            const previousCustomerValues = @json(old());
            if (failedCustomerId) openEditModal(failedCustomerId);
            else openAddModal();
            ['name', 'reporter_name', 'reporter_contact', 'area', 'address', 'latitude', 'longitude', 'registration_date', 'package_name', 'status'].forEach(name => {
                if (Object.prototype.hasOwnProperty.call(previousCustomerValues, name)) {
                    const field = customerForm.elements[name];
                    if (field && !field.disabled) field.value = previousCustomerValues[name] || '';
                }
            });
            if (previousCustomerValues.name && !failedCustomerId) {
                customerNameLabel.textContent = previousCustomerValues.name;
            }
            if (previousCustomerValues.area) customerAreaSearch.value = previousCustomerValues.area;
            updateCustomerCodePreview();
            if (!failedCustomerId && previousCustomerValues.latitude && previousCustomerValues.longitude) {
                window.setTimeout(() => {
                    showCustomerMapPoint(previousCustomerValues.latitude, previousCustomerValues.longitude);
                }, 300);
            }
        @endif

        ['customer-latitude', 'customer-longitude'].forEach(id => {
            document.getElementById(id).addEventListener('change', () => {
                if (isApplyingLocation) return;
                const latitudeValue = document.getElementById('customer-latitude').value;
                const longitudeValue = document.getElementById('customer-longitude').value;
                if (!latitudeValue || !longitudeValue) return;
                lookupAddressFromCoordinates(Number(latitudeValue), Number(longitudeValue));
            });
        });

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
