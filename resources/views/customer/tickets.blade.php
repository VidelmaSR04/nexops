<x-app-layout>
    <div class="mx-auto max-w-5xl space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-dark">Tiket Saya</h1>
            <p class="mt-1 text-sm text-secondary">Laporkan gangguan layanan TIK dan pantau tindak lanjutnya.</p>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700" role="status">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        @if (! $customer)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800">
                Akunmu belum dihubungkan Admin ke data OPD. Hubungi Admin agar akun dapat digunakan untuk mengirim tiket.
            </div>
        @elseif ($customer->type !== 'opd' || $customer->status !== 'Aktif')
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800">
                OPD {{ $customer->name }} belum aktif. Pengiriman tiket tersedia setelah status OPD diaktifkan Admin.
            </div>
        @else
            <section class="rounded-2xl border border-border bg-white p-6 shadow-sm">
                <h2 class="mb-1 text-lg font-semibold text-dark">Buat Tiket Gangguan</h2>
                <p class="mb-5 text-sm text-secondary">Tiket akan tercatat untuk OPD {{ $customer->name }}.</p>

                <form action="{{ route('customer.tickets.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="reporter_name" class="mb-1 block text-sm font-medium text-dark">Nama Pelapor</label>
                            <input id="reporter_name" name="reporter_name" value="{{ old('reporter_name', auth()->user()->name) }}" required maxlength="150" class="w-full rounded-xl border border-border px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label for="reporter_contact" class="mb-1 block text-sm font-medium text-dark">Kontak Pelapor</label>
                            <input id="reporter_contact" name="reporter_contact" type="tel" value="{{ old('reporter_contact', $customer->reporter_contact) }}" required maxlength="14" inputmode="numeric" pattern="[0-9]{1,14}" class="w-full rounded-xl border border-border px-3 py-2 text-sm">
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="issue_category" class="mb-1 block text-sm font-medium text-dark">Kategori Gangguan</label>
                            <select id="issue_category" name="issue_category" required class="w-full rounded-xl border border-border bg-white px-3 py-2 text-sm">
                                <option value="">Pilih kategori</option>
                                @foreach ($issueCategories as $category)
                                    <option value="{{ $category }}" @selected(old('issue_category') === $category)>{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="priority" class="mb-1 block text-sm font-medium text-dark">Prioritas</label>
                            <select id="priority" name="priority" required class="w-full rounded-xl border border-border bg-white px-3 py-2 text-sm">
                                @foreach (['Low' => 'Rendah', 'Normal' => 'Normal', 'High' => 'Tinggi', 'Critical' => 'Kritis'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('priority', 'Normal') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="title" class="mb-1 block text-sm font-medium text-dark">Judul Keluhan</label>
                        <input id="title" name="title" value="{{ old('title') }}" required maxlength="255" class="w-full rounded-xl border border-border px-3 py-2 text-sm">
                    </div>

                    <div>
                        <label for="location" class="mb-1 block text-sm font-medium text-dark">Lokasi Gangguan</label>
                        <input id="location" name="location" value="{{ old('location') }}" required maxlength="100" placeholder="Contoh: ruang server lantai 2" class="w-full rounded-xl border border-border px-3 py-2 text-sm">
                    </div>

                    <div>
                        <label for="description" class="mb-1 block text-sm font-medium text-dark">Deskripsi Keluhan</label>
                        <textarea id="description" name="description" rows="4" required maxlength="10000" class="w-full rounded-xl border border-border px-3 py-2 text-sm">{{ old('description') }}</textarea>
                    </div>

                    <div class="flex justify-end border-t border-border pt-4">
                        <button type="submit" class="rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Kirim Tiket</button>
                    </div>
                </form>
            </section>
        @endif

        <section class="overflow-hidden rounded-2xl border border-border bg-white shadow-sm">
            <div class="border-b border-border px-5 py-4">
                <h2 class="font-semibold text-dark">Riwayat Tiket</h2>
            </div>
            @if ($tickets->isEmpty())
                <p class="p-5 text-sm text-secondary">Belum ada tiket yang tercatat untuk akun OPD ini.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-surface text-xs uppercase text-secondary">
                            <tr>
                                <th class="px-5 py-3">Nomor / Tanggal</th>
                                <th class="px-5 py-3">Keluhan</th>
                                <th class="px-5 py-3">Prioritas</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Teknisi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($tickets as $ticket)
                                <tr>
                                    <td class="px-5 py-3">
                                        <p class="font-semibold text-primary">{{ $ticket->ticket_number }}</p>
                                        <p class="text-xs text-secondary">{{ $ticket->created_at?->format('d M Y H:i') ?? '-' }}</p>
                                    </td>
                                    <td class="px-5 py-3">
                                        <p class="font-medium text-dark">{{ $ticket->title }}</p>
                                        <p class="text-xs text-secondary">{{ $ticket->issue_category ?? '-' }} · {{ $ticket->location }}</p>
                                    </td>
                                    <td class="px-5 py-3">{{ $ticket->priority }}</td>
                                    <td class="px-5 py-3">{{ $ticket->status }}</td>
                                    <td class="px-5 py-3">{{ $ticket->activeTechnicians->first()?->name ?? 'Menunggu penugasan' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
