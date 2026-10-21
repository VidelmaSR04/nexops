<x-app-layout>
    <div class="mx-auto max-w-3xl space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-dark">Buat Tiket Baru</h1>
                <p class="mt-1 text-sm text-secondary">Isi informasi tiket operasional.</p>
            </div>
            <a href="{{ route('tickets.index') }}" class="rounded-xl border border-border px-4 py-2.5 text-sm font-medium text-dark hover:bg-gray-50">Kembali</a>
        </div>

        <form action="{{ route('tickets.store') }}" method="POST" class="space-y-5 rounded-2xl border border-border bg-white p-6 shadow-sm">
            @csrf

            <div>
                <label for="customer_id" class="mb-1 block text-sm font-medium text-dark">OPD Pelapor</label>
                <select id="customer_id" name="customer_id" required class="w-full rounded-xl border border-border bg-white px-3 py-2 text-sm">
                    <option value="">Pilih OPD</option>
                    @foreach ($opds as $opd)
                        <option value="{{ $opd->id }}" data-reporter="{{ $opd->reporter_name }}" data-contact="{{ $opd->reporter_contact }}" @selected(old('customer_id') == $opd->id)>{{ $opd->name }}</option>
                    @endforeach
                </select>
                @error('customer_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="reporter_name" class="mb-1 block text-sm font-medium text-dark">Nama Pelapor</label>
                    <input id="reporter_name" type="text" name="reporter_name" value="{{ old('reporter_name') }}" required maxlength="150" class="w-full rounded-xl border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                    @error('reporter_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="reporter_contact" class="mb-1 block text-sm font-medium text-dark">Kontak Pelapor</label>
                    <input id="reporter_contact" type="text" name="reporter_contact" value="{{ old('reporter_contact') }}" maxlength="50" class="w-full rounded-xl border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                    @error('reporter_contact') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="location" class="mb-1 block text-sm font-medium text-dark">Lokasi</label>
                    <input id="location" type="text" name="location" value="{{ old('location') }}" required maxlength="100" class="w-full rounded-xl border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                    @error('location') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="priority" class="mb-1 block text-sm font-medium text-dark">Prioritas</label>
                    <select id="priority" name="priority" required class="w-full rounded-xl border border-border bg-white px-3 py-2 text-sm">
                        @foreach (['Low', 'Normal', 'High', 'Critical'] as $priority)
                            <option value="{{ $priority }}" @selected(old('priority', 'Normal') === $priority)>{{ $priority }}</option>
                        @endforeach
                    </select>
                    @error('priority') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="title" class="mb-1 block text-sm font-medium text-dark">Judul Masalah</label>
                <input id="title" type="text" name="title" value="{{ old('title') }}" required maxlength="255" class="w-full rounded-xl border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="mb-1 block text-sm font-medium text-dark">Deskripsi</label>
                <textarea id="description" name="description" rows="4" class="w-full rounded-xl border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="assigned_tech_name" class="mb-1 block text-sm font-medium text-dark">Teknisi</label>
                    <select id="assigned_tech_name" name="assigned_tech_name" class="w-full rounded-xl border border-border bg-white px-3 py-2 text-sm">
                        <option value="-">Belum ditugaskan</option>
                        @foreach ($technicians as $technician)
                            <option value="{{ $technician }}" @selected(old('assigned_tech_name') === $technician)>{{ $technician }}</option>
                        @endforeach
                    </select>
                    @error('assigned_tech_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="asset_name" class="mb-1 block text-sm font-medium text-dark">Perangkat / Aset</label>
                    <input id="asset_name" type="text" name="asset_name" value="{{ old('asset_name') }}" class="w-full rounded-xl border border-border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                    @error('asset_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-border pt-4">
                <a href="{{ route('tickets.index') }}" class="rounded-xl border border-border px-4 py-2.5 text-sm font-medium text-secondary hover:bg-gray-50">Batal</a>
                <button type="submit" class="rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Simpan Tiket</button>
            </div>
        </form>
    </div>
    <script>
        document.getElementById('customer_id').addEventListener('change', function () {
            const option = this.selectedOptions[0];
            document.getElementById('reporter_name').value = option?.dataset.reporter || '';
            document.getElementById('reporter_contact').value = option?.dataset.contact || '';
        });
    </script>
</x-app-layout>
