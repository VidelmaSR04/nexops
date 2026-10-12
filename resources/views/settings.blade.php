<x-app-layout>
    <div class="space-y-6">

        {{-- HEADER SETTINGS --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-dark tracking-tight">Settings</h1>
                <p class="text-secondary text-sm mt-0.5">Kelola pengaturan sistem dan preferensi aplikasi</p>
            </div>
        </div>

        {{-- LAYOUT UTAMA: KATEGORI (KIRI) & KONTEN (KANAN) --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">

            {{-- PANEL NAVIGASI KATEGORI (KIRI) --}}
            <div class="lg:col-span-1 bg-white rounded-2xl border border-border shadow-[0_2px_6px_rgba(0,0,0,0.02)] p-3 space-y-1">
                <button type="button" onclick="switchSettingsTab('general')" data-tab="general" class="settings-nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-colors bg-primary text-white">
                    <i data-lucide="sliders" class="w-4 h-4"></i>
                    <div class="text-left flex-1">
                        <p class="font-semibold">General</p>
                        <p class="text-[10px] opacity-80">Informasi & preferensi</p>
                    </div>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 opacity-70"></i>
                </button>

                <button type="button" onclick="switchSettingsTab('users')" data-tab="users" class="settings-nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-colors text-secondary hover:bg-gray-50 hover:text-dark">
                    <i data-lucide="users" class="w-4 h-4"></i>
                    <div class="text-left flex-1">
                        <p class="font-semibold">User Management</p>
                        <p class="text-[10px] text-gray-400">Pengguna & hak akses</p>
                    </div>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 opacity-40"></i>
                </button>

                <button type="button" onclick="switchSettingsTab('notification')" data-tab="notification" class="settings-nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-colors text-secondary hover:bg-gray-50 hover:text-dark">
                    <i data-lucide="bell" class="w-4 h-4"></i>
                    <div class="text-left flex-1">
                        <p class="font-semibold">Notification</p>
                        <p class="text-[10px] text-gray-400">Pengaturan alert & email</p>
                    </div>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 opacity-40"></i>
                </button>

                <button type="button" onclick="switchSettingsTab('system')" data-tab="system" class="settings-nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-colors text-secondary hover:bg-gray-50 hover:text-dark">
                    <i data-lucide="cpu" class="w-4 h-4"></i>
                    <div class="text-left flex-1">
                        <p class="font-semibold">System</p>
                        <p class="text-[10px] text-gray-400">Informasi sistem & versi</p>
                    </div>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 opacity-40"></i>
                </button>

                <button type="button" onclick="switchSettingsTab('security')" data-tab="security" class="settings-nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-colors text-secondary hover:bg-gray-50 hover:text-dark">
                    <i data-lucide="shield" class="w-4 h-4"></i>
                    <div class="text-left flex-1">
                        <p class="font-semibold">Security</p>
                        <p class="text-[10px] text-gray-400">Password, 2FA & sesi</p>
                    </div>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 opacity-40"></i>
                </button>

                <button type="button" onclick="switchSettingsTab('backup')" data-tab="backup" class="settings-nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-colors text-secondary hover:bg-gray-50 hover:text-dark">
                    <i data-lucide="database" class="w-4 h-4"></i>
                    <div class="text-left flex-1">
                        <p class="font-semibold">Backup</p>
                        <p class="text-[10px] text-gray-400">Cadangkan data sistem</p>
                    </div>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 opacity-40"></i>
                </button>

                <button type="button" onclick="switchSettingsTab('integrations')" data-tab="integrations" class="settings-nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-colors text-secondary hover:bg-gray-50 hover:text-dark">
                    <i data-lucide="grid" class="w-4 h-4"></i>
                    <div class="text-left flex-1">
                        <p class="font-semibold">Integrations</p>
                        <p class="text-[10px] text-gray-400">Layanan pihak ketiga</p>
                    </div>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 opacity-40"></i>
                </button>
            </div>

            {{-- PANEL KONTEN PENGATURAN (KANAN) --}}
            <div class="lg:col-span-3 space-y-5">

                {{-- TAB 1: GENERAL --}}
                <div id="settings-pane-general" class="settings-pane space-y-5">
                    {{-- Informasi Perusahaan --}}
                    <div class="bg-white rounded-2xl border border-border p-5 shadow-[0_2px_6px_rgba(0,0,0,0.02)] space-y-4">
                        <div>
                            <h3 class="text-base font-bold text-dark">Informasi Perusahaan</h3>
                            <p class="text-secondary text-xs">Kelola informasi dasar perusahaan</p>
                        </div>
                        <form onsubmit="handleSettingsSubmit(event)" class="space-y-3.5 text-xs">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-medium text-dark mb-1">Nama Perusahaan</label>
                                    <input type="text" value="NEXOPS" required class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                </div>
                                <div>
                                    <label class="block font-medium text-dark mb-1">Email Resmi</label>
                                    <input type="email" value="info@nexops.id" required class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-medium text-dark mb-1">Nomor Telepon</label>
                                    <input type="text" value="(0338) 123456" required class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                </div>
                                <div>
                                    <label class="block font-medium text-dark mb-1">Logo Perusahaan (PNG/JPG, maks 2MB)</label>
                                    <input type="file" accept=".png, .jpg, .jpeg" class="w-full text-xs text-secondary file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-primary hover:file:bg-blue-100 cursor-pointer">
                                </div>
                            </div>
                            <div>
                                <label class="block font-medium text-dark mb-1">Alamat Lengkap</label>
                                <textarea rows="2" required class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary">Jl. Raya Situbondo No. 123, Situbondo, Jawa Timur 68312</textarea>
                            </div>
                            <div class="pt-2 flex justify-end">
                                <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-blue-700 shadow-sm transition-colors">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>

                    {{-- Preferensi Aplikasi --}}
                    <div class="bg-white rounded-2xl border border-border p-5 shadow-[0_2px_6px_rgba(0,0,0,0.02)] space-y-4">
                        <div>
                            <h3 class="text-base font-bold text-dark">Preferensi Aplikasi</h3>
                            <p class="text-secondary text-xs">Atur tampilan tema, bahasa, dan zona waktu</p>
                        </div>
                        <form onsubmit="handleSettingsSubmit(event)" class="space-y-3.5 text-xs">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                                <div>
                                    <label class="block font-medium text-dark mb-1">Tema</label>
                                    <select class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                                        <option value="light" selected>Light</option>
                                        <option value="dark">Dark</option>
                                        <option value="system">System</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-medium text-dark mb-1">Bahasa</label>
                                    <select class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                                        <option value="id" selected>Indonesia</option>
                                        <option value="en">English</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-medium text-dark mb-1">Zona Waktu</label>
                                    <select class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                                        <option value="WIB" selected>Waktu Indonesia Barat (WIB)</option>
                                        <option value="WITA">Waktu Indonesia Tengah (WITA)</option>
                                        <option value="WIT">Waktu Indonesia Timur (WIT)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="pt-2 flex justify-end">
                                <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-blue-700 shadow-sm transition-colors">Simpan Preferensi</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- TAB 2: USER MANAGEMENT --}}
                <div id="settings-pane-users" class="settings-pane hidden space-y-5">
                    <div class="bg-white rounded-2xl border border-border p-5 shadow-[0_2px_6px_rgba(0,0,0,0.02)] space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <h3 class="text-base font-bold text-dark">User Management</h3>
                                <p class="text-secondary text-xs">Daftar pengguna dan hak akses sistem</p>
                            </div>
                            <button type="button" onclick="openModal('modal-add-user')" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-primary text-white text-xs font-medium hover:bg-blue-700 shadow-sm transition-colors">
                                <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                                <span>Tambah Pengguna</span>
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left">
                                <thead>
                                    <tr class="text-secondary bg-[#FAFCFF] border-b border-border font-medium text-[11px] uppercase tracking-wider">
                                        <th class="py-3 pl-3">Nama</th>
                                        <th class="py-3 pr-3">Email</th>
                                        <th class="py-3 pr-3">Role</th>
                                        <th class="py-3 pr-3">Status</th>
                                        <th class="py-3 pr-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr>
                                        <td class="py-3 pl-3 font-semibold text-dark">Eka Pratama</td>
                                        <td class="py-3 pr-3 text-secondary">eka@nexops.id</td>
                                        <td class="py-3 pr-3"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-primary">Administrator</span></td>
                                        <td class="py-3 pr-3"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-600">Aktif</span></td>
                                        <td class="py-3 pr-3 text-center">
                                            <button type="button" class="p-1 rounded text-gray-400 hover:text-dark hover:bg-gray-100" onclick="showToast('Simulasi Edit User')"><i data-lucide="edit-2" class="w-3.5 h-3.5"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 pl-3 font-semibold text-dark">Budi Santoso</td>
                                        <td class="py-3 pr-3 text-secondary">budi.tech@nexops.id</td>
                                        <td class="py-3 pr-3"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-600">Teknisi</span></td>
                                        <td class="py-3 pr-3"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-600">Aktif</span></td>
                                        <td class="py-3 pr-3 text-center">
                                            <button type="button" class="p-1 rounded text-gray-400 hover:text-dark hover:bg-gray-100" onclick="showToast('Simulasi Edit User')"><i data-lucide="edit-2" class="w-3.5 h-3.5"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- TAB 3: NOTIFICATION --}}
                <div id="settings-pane-notification" class="settings-pane hidden space-y-5">
                    <div class="bg-white rounded-2xl border border-border p-5 shadow-[0_2px_6px_rgba(0,0,0,0.02)] space-y-4">
                        <div>
                            <h3 class="text-base font-bold text-dark">Pengaturan Notifikasi</h3>
                            <p class="text-secondary text-xs">Pilih notifikasi operasional yang ingin diterima</p>
                        </div>
                        <div class="space-y-4 pt-2 text-xs">
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <div>
                                    <p class="font-semibold text-dark">Notifikasi Ticket Baru</p>
                                    <p class="text-secondary text-[11px]">Terima notifikasi saat ada ticket baru masuk.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <div>
                                    <p class="font-semibold text-dark">Notifikasi Ticket Selesai</p>
                                    <p class="text-secondary text-[11px]">Terima notifikasi saat ticket berhasil diselesaikan.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <div>
                                    <p class="font-semibold text-dark">Notifikasi Ticket Overdue</p>
                                    <p class="text-secondary text-[11px]">Terima notifikasi saat ada ticket melewati SLA (terlambat).</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <div>
                                    <p class="font-semibold text-dark">Laporan Mingguan</p>
                                    <p class="text-secondary text-[11px]">Terima ringkasan laporan operasional mingguan via email.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                            <div class="pt-3 flex justify-end">
                                <button type="button" onclick="showToast('Pengaturan notifikasi disimpan!')" class="px-4 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-blue-700 shadow-sm transition-colors">Simpan Pengaturan</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB 4: SYSTEM --}}
                <div id="settings-pane-system" class="settings-pane hidden space-y-5">
                    <div class="bg-white rounded-2xl border border-border p-5 shadow-[0_2px_6px_rgba(0,0,0,0.02)] space-y-4">
                        <div>
                            <h3 class="text-base font-bold text-dark">Informasi Sistem</h3>
                            <p class="text-secondary text-xs">Detail teknis aplikasi dan lingkungan server (Simulasi)</p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div class="p-3 rounded-xl border border-border bg-surface">
                                <p class="text-secondary text-[11px]">Nama Aplikasi</p>
                                <p class="font-bold text-dark mt-0.5">NEXOPS — Operations Management System</p>
                            </div>
                            <div class="p-3 rounded-xl border border-border bg-surface">
                                <p class="text-secondary text-[11px]">Versi Aplikasi</p>
                                <p class="font-bold text-dark mt-0.5">v1.0.4 Production</p>
                            </div>
                            <div class="p-3 rounded-xl border border-border bg-surface">
                                <p class="text-secondary text-[11px]">Environment</p>
                                <p class="font-bold text-dark mt-0.5">production</p>
                            </div>
                            <div class="p-3 rounded-xl border border-border bg-surface">
                                <p class="text-secondary text-[11px]">Status Sistem</p>
                                <p class="font-bold text-emerald-600 mt-0.5 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500 inline-block animate-pulse"></span> Normal (Operational)</p>
                            </div>
                            <div class="p-3 rounded-xl border border-border bg-surface">
                                <p class="text-secondary text-[11px]">PHP Version</p>
                                <p class="font-bold text-dark mt-0.5">PHP 8.2.29</p>
                            </div>
                            <div class="p-3 rounded-xl border border-border bg-surface">
                                <p class="text-secondary text-[11px]">Laravel Version</p>
                                <p class="font-bold text-dark mt-0.5">Laravel 10.x</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB 5: SECURITY --}}
                <div id="settings-pane-security" class="settings-pane hidden space-y-5">
                    <div class="bg-white rounded-2xl border border-border p-5 shadow-[0_2px_6px_rgba(0,0,0,0.02)] space-y-4">
                        <div>
                            <h3 class="text-base font-bold text-dark">Pengaturan Keamanan</h3>
                            <p class="text-secondary text-xs">Kelola password, 2FA, riwayat login, dan sesi perangkat</p>
                        </div>
                        <div class="space-y-3.5 text-xs pt-2">
                            <div class="flex items-center justify-between p-3.5 rounded-xl border border-border bg-surface">
                                <div>
                                    <p class="font-semibold text-dark">Password Akun</p>
                                    <p class="text-secondary text-[11px]">Perbarui password akun administrator Anda secara berkala.</p>
                                </div>
                                <button type="button" onclick="openModal('modal-change-password')" class="px-3.5 py-2 rounded-xl border border-border bg-white font-medium text-dark hover:bg-gray-50 shadow-sm">Ubah Password</button>
                            </div>

                            <div class="flex items-center justify-between p-3.5 rounded-xl border border-border bg-surface">
                                <div>
                                    <p class="font-semibold text-dark">Two-Factor Authentication (2FA)</p>
                                    <p class="text-secondary text-[11px]">Tingkatkan keamanan akun dengan verifikasi ganda.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" onchange="showToast('Simulasi status 2FA diperbarui')" class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>

                            <div class="flex items-center justify-between p-3.5 rounded-xl border border-border bg-surface">
                                <div>
                                    <p class="font-semibold text-dark">Riwayat Login</p>
                                    <p class="text-secondary text-[11px]">Pantau aktivitas login terakhir pada akun Anda.</p>
                                </div>
                                <button type="button" onclick="openModal('modal-login-history')" class="px-3.5 py-2 rounded-xl border border-border bg-white font-medium text-dark hover:bg-gray-50 shadow-sm">Lihat Riwayat</button>
                            </div>

                            <div class="flex items-center justify-between p-3.5 rounded-xl border border-border bg-surface">
                                <div>
                                    <p class="font-semibold text-dark">Manajemen Sesi Perangkat</p>
                                    <p class="text-secondary text-[11px]">Kelola dan putuskan sesi perangkat yang sedang aktif.</p>
                                </div>
                                <button type="button" onclick="openModal('modal-sessions')" class="px-3.5 py-2 rounded-xl border border-border bg-white font-medium text-dark hover:bg-gray-50 shadow-sm">Kelola Sesi</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB 6: BACKUP --}}
                <div id="settings-pane-backup" class="settings-pane hidden space-y-5">
                    <div class="bg-white rounded-2xl border border-border p-5 shadow-[0_2px_6px_rgba(0,0,0,0.02)] space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <h3 class="text-base font-bold text-dark">Backup & Restore</h3>
                                <p class="text-secondary text-xs">Cadangkan data dan konfigurasi sistem operasional</p>
                            </div>
                            <button type="button" onclick="showToast('Simulasi: Backup sistem berhasil dibuat!')" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-primary text-white text-xs font-medium hover:bg-blue-700 shadow-sm transition-colors">
                                <i data-lucide="database" class="w-3.5 h-3.5"></i>
                                <span>Buat Backup Baru</span>
                            </button>
                        </div>
                        <div class="p-4 rounded-xl border border-border bg-surface text-xs space-y-2">
                            <p class="font-semibold text-dark">Status Backup Terakhir:</p>
                            <p class="text-secondary">Backup otomatis berhasil dijalankan pada <strong class="text-dark">12 Okt 2026, 02:00 WIB</strong> (File: <code class="bg-gray-100 px-1 py-0.5 rounded text-primary">nexops_backup_20261012.zip</code>)</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left">
                                <thead>
                                    <tr class="text-secondary bg-[#FAFCFF] border-b border-border font-medium text-[11px] uppercase tracking-wider">
                                        <th class="py-3 pl-3">Nama File Backup</th>
                                        <th class="py-3 pr-3">Tanggal</th>
                                        <th class="py-3 pr-3">Ukuran</th>
                                        <th class="py-3 pr-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr>
                                        <td class="py-3 pl-3 font-medium text-dark">nexops_backup_20261012.zip</td>
                                        <td class="py-3 pr-3 text-secondary">12 Okt 2026 02:00</td>
                                        <td class="py-3 pr-3 text-secondary">14.2 MB</td>
                                        <td class="py-3 pr-3 text-center">
                                            <button type="button" onclick="showToast('Mengunduh file backup dummy...')" class="px-2.5 py-1 rounded-lg border border-border text-primary font-medium hover:bg-blue-50">Download</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- TAB 7: INTEGRATIONS --}}
                <div id="settings-pane-integrations" class="settings-pane hidden space-y-5">
                    <div class="bg-white rounded-2xl border border-border p-5 shadow-[0_2px_6px_rgba(0,0,0,0.02)] space-y-4">
                        <div>
                            <h3 class="text-base font-bold text-dark">Integrasi Layanan Pihak Ketiga</h3>
                            <p class="text-secondary text-xs">Hubungkan NEXOPS dengan layanan eksternal</p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="p-4 rounded-xl border border-border bg-surface space-y-3 flex flex-col justify-between">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center"><i data-lucide="mail" class="w-4 h-4"></i></div>
                                        <div>
                                            <p class="font-bold text-dark">Email Service (SMTP)</p>
                                            <p class="text-[11px] text-secondary">Kirim notifikasi & invoice</p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-600">Connected</span>
                                </div>
                                <button type="button" onclick="showToast('Konfigurasi Email Service')" class="w-full py-1.5 rounded-lg border border-border bg-white font-medium text-dark hover:bg-gray-50 text-center">Konfigurasi</button>
                            </div>

                            <div class="p-4 rounded-xl border border-border bg-surface space-y-3 flex flex-col justify-between">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center"><i data-lucide="message-square" class="w-4 h-4"></i></div>
                                        <div>
                                            <p class="font-bold text-dark">WhatsApp Gateway</p>
                                            <p class="text-[11px] text-secondary">Blast info tagihan & tiket</p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-600">Connected</span>
                                </div>
                                <button type="button" onclick="showToast('Konfigurasi WhatsApp Gateway')" class="w-full py-1.5 rounded-lg border border-border bg-white font-medium text-dark hover:bg-gray-50 text-center">Konfigurasi</button>
                            </div>

                            <div class="p-4 rounded-xl border border-border bg-surface space-y-3 flex flex-col justify-between">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center"><i data-lucide="hard-drive" class="w-4 h-4"></i></div>
                                        <div>
                                            <p class="font-bold text-dark">Cloud Storage (S3)</p>
                                            <p class="text-[11px] text-secondary">Penyimpanan dokumen & foto</p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-500">Not Connected</span>
                                </div>
                                <button type="button" onclick="showToast('Hubungkan Cloud Storage')" class="w-full py-1.5 rounded-lg border border-border bg-white font-medium text-dark hover:bg-gray-50 text-center">Hubungkan</button>
                            </div>

                            <div class="p-4 rounded-xl border border-border bg-surface space-y-3 flex flex-col justify-between">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center"><i data-lucide="activity" class="w-4 h-4"></i></div>
                                        <div>
                                            <p class="font-bold text-dark">Monitoring Tool</p>
                                            <p class="text-[11px] text-secondary">Pantau status server & ONT</p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-600">Connected</span>
                                </div>
                                <button type="button" onclick="showToast('Konfigurasi Monitoring Tool')" class="w-full py-1.5 rounded-lg border border-border bg-white font-medium text-dark hover:bg-gray-50 text-center">Konfigurasi</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    {{-- MODALS PROTOTYPE --}}

    {{-- Modal Tambah Pengguna --}}
    <div id="modal-add-user" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs hidden">
        <div class="bg-white rounded-2xl border border-border shadow-2xl max-w-md w-full overflow-hidden">
            <div class="p-4 border-b border-border flex items-center justify-between">
                <h3 class="text-base font-bold text-dark">Tambah Pengguna Baru</h3>
                <button type="button" onclick="closeModal('modal-add-user')" class="p-1 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form onsubmit="handleModalSubmit(event, 'modal-add-user', 'Pengguna baru berhasil ditambahkan!')" class="p-5 space-y-3.5 text-xs">
                <div>
                    <label class="block font-medium text-dark mb-1">Nama Lengkap</label>
                    <input type="text" required placeholder="Contoh: Rian Utama" class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1">Email</label>
                    <input type="email" required placeholder="rian@nexops.id" class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1">Role / Hak Akses</label>
                    <select class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                        <option value="Administrator">Administrator</option>
                        <option value="Teknisi">Teknisi</option>
                        <option value="Finance">Finance</option>
                    </select>
                </div>
                <div class="pt-3 border-t border-border flex items-center justify-end gap-2">
                    <button type="button" onclick="closeModal('modal-add-user')" class="px-3.5 py-2 rounded-xl border border-border text-secondary hover:bg-gray-50 font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-blue-700 shadow-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Ubah Password --}}
    <div id="modal-change-password" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs hidden">
        <div class="bg-white rounded-2xl border border-border shadow-2xl max-w-md w-full overflow-hidden">
            <div class="p-4 border-b border-border flex items-center justify-between">
                <h3 class="text-base font-bold text-dark">Ubah Password Akun</h3>
                <button type="button" onclick="closeModal('modal-change-password')" class="p-1 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form onsubmit="handleModalSubmit(event, 'modal-change-password', 'Password berhasil diubah (Simulasi)!')" class="p-5 space-y-3.5 text-xs">
                <div>
                    <label class="block font-medium text-dark mb-1">Password Lama</label>
                    <input type="password" required class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1">Password Baru</label>
                    <input type="password" required class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                </div>
                <div>
                    <label class="block font-medium text-dark mb-1">Konfirmasi Password Baru</label>
                    <input type="password" required class="w-full rounded-xl border border-border px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20">
                </div>
                <div class="pt-3 border-t border-border flex items-center justify-end gap-2">
                    <button type="button" onclick="closeModal('modal-change-password')" class="px-3.5 py-2 rounded-xl border border-border text-secondary hover:bg-gray-50 font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-blue-700 shadow-sm">Perbarui Password</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Login History --}}
    <div id="modal-login-history" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs hidden">
        <div class="bg-white rounded-2xl border border-border shadow-2xl max-w-2xl w-full overflow-hidden">
            <div class="p-4 border-b border-border flex items-center justify-between">
                <h3 class="text-base font-bold text-dark">Riwayat Login Akun</h3>
                <button type="button" onclick="closeModal('modal-login-history')" class="p-1 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <div class="p-5 overflow-x-auto text-xs">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-secondary bg-[#FAFCFF] border-b border-border font-medium text-[11px] uppercase tracking-wider">
                            <th class="py-2.5 pl-3">Tanggal / Waktu</th>
                            <th class="py-2.5 pr-3">Perangkat</th>
                            <th class="py-2.5 pr-3">Lokasi</th>
                            <th class="py-2.5 pr-3">IP Address</th>
                            <th class="py-2.5 pr-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr>
                            <td class="py-2.5 pl-3 font-medium text-dark">12 Okt 2026, 08:30</td>
                            <td class="py-2.5 pr-3">Chrome on Windows</td>
                            <td class="py-2.5 pr-3">Situbondo, ID</td>
                            <td class="py-2.5 pr-3 text-secondary">36.75.12.90</td>
                            <td class="py-2.5 pr-3"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-600">Berhasil</span></td>
                        </tr>
                        <tr>
                            <td class="py-2.5 pl-3 font-medium text-dark">10 Okt 2026, 14:15</td>
                            <td class="py-2.5 pr-3">Firefox on MacOS</td>
                            <td class="py-2.5 pr-3">Surabaya, ID</td>
                            <td class="py-2.5 pr-3 text-secondary">114.125.8.12</td>
                            <td class="py-2.5 pr-3"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-600">Berhasil</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Session Management --}}
    <div id="modal-sessions" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs hidden">
        <div class="bg-white rounded-2xl border border-border shadow-2xl max-w-2xl w-full overflow-hidden">
            <div class="p-4 border-b border-border flex items-center justify-between">
                <h3 class="text-base font-bold text-dark">Manajemen Sesi Perangkat</h3>
                <button type="button" onclick="closeModal('modal-sessions')" class="p-1 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <div class="p-5 space-y-3 text-xs">
                <div class="flex items-center justify-between p-3 rounded-xl border border-border bg-surface">
                    <div>
                        <p class="font-bold text-dark">Chrome on Windows (Aktif Saat Ini)</p>
                        <p class="text-secondary text-[11px]">Situbondo, Indonesia • IP: 36.75.12.90</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-600">Current Session</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl border border-border bg-surface">
                    <div>
                        <p class="font-bold text-dark">Firefox on MacOS</p>
                        <p class="text-secondary text-[11px]">Surabaya, Indonesia • Aktivitas terakhir 2 hari lalu</p>
                    </div>
                    <button type="button" onclick="showToast('Sesi perangkat berhasil diakhiri')" class="px-3 py-1 rounded-lg border border-red-200 text-danger font-medium hover:bg-red-50">Akhiri Sesi</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Notifikasi --}}
    <div id="toast-notification" class="fixed bottom-5 right-5 z-50 hidden max-w-sm rounded-xl bg-dark text-white text-xs px-4 py-3 shadow-xl transition-all"></div>

    {{-- JAVASCRIPT INTERAKTIF --}}
    <script>
        function switchSettingsTab(tabId) {
            document.querySelectorAll('.settings-nav-btn').forEach(btn => {
                btn.classList.remove('bg-primary', 'text-white');
                btn.classList.add('text-secondary', 'hover:bg-gray-50', 'hover:text-dark');
            });
            const activeBtn = document.querySelector(`[data-tab="${tabId}"]`);
            if (activeBtn) {
                activeBtn.classList.add('bg-primary', 'text-white');
                activeBtn.classList.remove('text-secondary', 'hover:bg-gray-50', 'hover:text-dark');
            }

            document.querySelectorAll('.settings-pane').forEach(pane => pane.classList.add('hidden'));
            const targetPane = document.getElementById(`settings-pane-${tabId}`);
            if (targetPane) targetPane.classList.remove('hidden');
        }

        let toastTimer;
        function showToast(message) {
            const toast = document.getElementById('toast-notification');
            toast.textContent = message;
            toast.classList.remove('hidden');
            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => toast.classList.add('hidden'), 2500);
        }

        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function handleSettingsSubmit(event) {
            event.preventDefault();
            showToast('Simulasi: Perubahan berhasil disimpan secara lokal!');
        }

        function handleModalSubmit(event, modalId, successMessage) {
            event.preventDefault();
            closeModal(modalId);
            showToast(successMessage);
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</x-app-layout>
