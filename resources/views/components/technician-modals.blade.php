@props(['areas' => [], 'statuses' => []])

{{-- Semua modal halaman Technicians (simulasi frontend, tidak ada request ke backend). --}}
{{-- Dibuka/ditutup lewat JavaScript di resources/views/technicians.blade.php.           --}}

@php
    $inputClass = 'w-full rounded-xl border border-border bg-white px-3 py-2 text-xs text-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary';
@endphp

{{-- ============================================ --}}
{{-- MODAL: TAMBAH / EDIT TEKNISI                 --}}
{{-- ============================================ --}}
<div id="modal-form" class="tech-modal fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 hidden" role="dialog" aria-modal="true" aria-labelledby="form-title">
    <div class="bg-white rounded-2xl border border-border shadow-2xl w-full max-w-lg max-h-[92vh] overflow-y-auto">
        <div class="p-4 border-b border-border flex items-start justify-between gap-3">
            <div>
                <h3 id="form-title" class="text-base font-bold text-dark">Tambah Teknisi</h3>
                <p id="form-sub" class="text-secondary text-xs mt-0.5">Isi data teknisi baru</p>
            </div>
            <button type="button" data-close-modal class="p-1.5 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100" aria-label="Tutup">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="form-tech" class="p-5 space-y-3.5 text-xs">
            <div>
                <label for="f-name" class="block font-medium text-dark mb-1">Nama Lengkap</label>
                <input id="f-name" type="text" required placeholder="Contoh: Budi Santoso" class="{{ $inputClass }}">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="f-nik" class="block font-medium text-dark mb-1">NIK</label>
                    <input id="f-nik" type="text" required placeholder="NIK001246" class="{{ $inputClass }}">
                </div>
                <div>
                    <label for="f-role" class="block font-medium text-dark mb-1">Jabatan</label>
                    <input id="f-role" type="text" required placeholder="Teknisi" class="{{ $inputClass }}">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="f-phone" class="block font-medium text-dark mb-1">Nomor Telepon</label>
                    <input id="f-phone" type="tel" required placeholder="0812 3456 7890" class="{{ $inputClass }}">
                </div>
                <div>
                    <label for="f-email" class="block font-medium text-dark mb-1">Email</label>
                    <input id="f-email" type="email" required placeholder="nama.teknisi@nexops.id" class="{{ $inputClass }}">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="f-area" class="block font-medium text-dark mb-1">Area</label>
                    <select id="f-area" class="{{ $inputClass }}">
                        @foreach ($areas as $area)
                            <option value="{{ $area }}">{{ $area }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="f-status" class="block font-medium text-dark mb-1">Status</label>
                    <select id="f-status" class="{{ $inputClass }}">
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="f-joined" class="block font-medium text-dark mb-1">Tanggal Bergabung</label>
                    <input id="f-joined" type="date" required class="{{ $inputClass }}">
                </div>
                <div>
                    <label for="f-exp" class="block font-medium text-dark mb-1">Pengalaman (tahun)</label>
                    <input id="f-exp" type="number" min="0" max="50" required value="0" class="{{ $inputClass }}">
                </div>
            </div>
            <div class="pt-3 border-t border-border flex items-center justify-end gap-2">
                <button type="button" data-close-modal class="px-3.5 py-2 rounded-xl border border-border text-secondary hover:bg-gray-50 font-medium">Batal</button>
                <button id="form-submit" type="submit" class="px-4 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-blue-700 shadow-sm">Simpan Teknisi</button>
            </div>
        </form>
    </div>
</div>

{{-- ============================================ --}}
{{-- MODAL: ASSIGN TICKET                         --}}
{{-- ============================================ --}}
<div id="modal-assign" class="tech-modal fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 hidden" role="dialog" aria-modal="true" aria-labelledby="assign-title">
    <div class="bg-white rounded-2xl border border-border shadow-2xl w-full max-w-lg max-h-[92vh] overflow-y-auto">
        <div class="p-4 border-b border-border flex items-start justify-between gap-3">
            <div>
                <h3 id="assign-title" class="text-base font-bold text-dark">Assign Ticket</h3>
                <p class="text-secondary text-xs mt-0.5">Tugaskan ticket kepada <span id="assign-name" class="font-semibold text-dark"></span></p>
            </div>
            <button type="button" data-close-modal class="p-1.5 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100" aria-label="Tutup">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="form-assign" class="p-5 space-y-3 text-xs">
            <p id="assign-warning" class="hidden rounded-xl border border-amber-200 bg-amber-50 text-amber-700 px-3 py-2"></p>
            <div id="assign-list" class="space-y-2"></div>
            <div class="pt-3 border-t border-border flex items-center justify-end gap-2">
                <button type="button" data-close-modal class="px-3.5 py-2 rounded-xl border border-border text-secondary hover:bg-gray-50 font-medium">Batal</button>
                <button id="assign-confirm" type="submit" disabled class="px-4 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-blue-700 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">Assign Ticket</button>
            </div>
        </form>
    </div>
</div>

{{-- ============================================ --}}
{{-- MODAL: LIHAT LOKASI (SIMULASI)               --}}
{{-- ============================================ --}}
<div id="modal-location" class="tech-modal fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 hidden" role="dialog" aria-modal="true" aria-labelledby="location-title">
    <div class="bg-white rounded-2xl border border-border shadow-2xl w-full max-w-lg max-h-[92vh] overflow-y-auto">
        <div class="p-4 border-b border-border flex items-start justify-between gap-3">
            <div>
                <h3 id="location-title" class="text-base font-bold text-dark">Lokasi Teknisi</h3>
                <p id="location-sub" class="text-secondary text-xs mt-0.5"></p>
            </div>
            <button type="button" data-close-modal class="p-1.5 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100" aria-label="Tutup">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="p-5 space-y-3 text-xs">
            {{-- Peta simulasi (tanpa GPS / API peta) --}}
            <div class="relative h-56 rounded-xl border border-border overflow-hidden bg-[#EAF2FC]"
                 style="background-image: linear-gradient(#D5E4F7 1px, transparent 1px), linear-gradient(90deg, #D5E4F7 1px, transparent 1px); background-size: 28px 28px;">
                <div class="absolute left-0 right-0 top-1/2 h-3 -translate-y-1/2 bg-white/80 -rotate-6"></div>
                <div class="absolute top-0 bottom-0 left-1/3 w-3 bg-white/80 rotate-12"></div>
                <div id="location-pin" class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-full flex flex-col items-center">
                    <span class="px-2 py-0.5 rounded-full bg-primary text-white text-[10px] font-semibold shadow" id="location-pin-label"></span>
                    <i data-lucide="map-pin" class="w-8 h-8 text-primary drop-shadow"></i>
                </div>
                <div id="location-off" class="hidden absolute inset-0 bg-white/70 backdrop-blur-[1px] items-center justify-center text-secondary font-medium">
                    Lokasi tidak tersedia
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-xl bg-surface border border-border p-3">
                    <p class="text-[11px] text-secondary">Area</p>
                    <p id="location-area" class="font-semibold text-dark mt-0.5"></p>
                </div>
                <div class="rounded-xl bg-surface border border-border p-3">
                    <p class="text-[11px] text-secondary">Pembaruan Terakhir</p>
                    <p id="location-updated" class="font-semibold text-dark mt-0.5"></p>
                </div>
            </div>
            <p class="text-[11px] text-secondary">Tampilan simulasi. Belum terhubung ke data GPS atau layanan peta.</p>
            <div class="pt-3 border-t border-border flex justify-end">
                <button type="button" data-close-modal class="px-4 py-2 rounded-xl border border-border text-dark hover:bg-gray-50 font-medium">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- MODAL: LIHAT JADWAL (DUMMY)                  --}}
{{-- ============================================ --}}
<div id="modal-schedule" class="tech-modal fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 hidden" role="dialog" aria-modal="true" aria-labelledby="schedule-title">
    <div class="bg-white rounded-2xl border border-border shadow-2xl w-full max-w-lg max-h-[92vh] overflow-y-auto">
        <div class="p-4 border-b border-border flex items-start justify-between gap-3">
            <div>
                <h3 id="schedule-title" class="text-base font-bold text-dark">Jadwal Mingguan</h3>
                <p id="schedule-sub" class="text-secondary text-xs mt-0.5"></p>
            </div>
            <button type="button" data-close-modal class="p-1.5 rounded-lg text-gray-400 hover:text-dark hover:bg-gray-100" aria-label="Tutup">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="p-5 space-y-3 text-xs">
            <ul id="schedule-body" class="space-y-2"></ul>
            <div class="pt-3 border-t border-border flex justify-end">
                <button type="button" data-close-modal class="px-4 py-2 rounded-xl border border-border text-dark hover:bg-gray-50 font-medium">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- MODAL: KONFIRMASI NONAKTIFKAN / AKTIFKAN     --}}
{{-- ============================================ --}}
<div id="modal-confirm" class="tech-modal fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 hidden" role="alertdialog" aria-modal="true" aria-labelledby="confirm-title">
    <div class="bg-white rounded-2xl border border-border shadow-2xl w-full max-w-sm">
        <div class="p-5 text-center space-y-3 text-xs">
            <div id="confirm-icon" class="mx-auto w-12 h-12 rounded-full bg-red-50 text-danger flex items-center justify-center">
                <i data-lucide="user-x" class="w-6 h-6"></i>
            </div>
            <h3 id="confirm-title" class="text-base font-bold text-dark"></h3>
            <p id="confirm-text" class="text-secondary leading-relaxed"></p>
            <p id="confirm-warning" class="hidden rounded-xl border border-amber-200 bg-amber-50 text-amber-700 px-3 py-2"></p>
        </div>
        <div class="p-4 border-t border-border flex items-center justify-end gap-2 text-xs">
            <button type="button" data-close-modal class="px-3.5 py-2 rounded-xl border border-border text-secondary hover:bg-gray-50 font-medium">Batal</button>
            <button id="confirm-ok" type="button" class="px-4 py-2 rounded-xl bg-danger text-white font-semibold hover:bg-red-600 shadow-sm"></button>
        </div>
    </div>
</div>
