<x-app-layout>
    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700" role="status">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-dark">{{ $ticket->ticket_number }}</h1>
                <p class="mt-1 text-sm text-secondary">Detail tiket operasional</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('tickets.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-border px-4 py-2.5 text-sm font-medium text-dark hover:bg-gray-50">
                    <i data-lucide="arrow-left" class="h-4 w-4"></i>
                    Kembali
                </a>
                <a href="{{ route('tickets.edit', $ticket->id) }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                    <i data-lucide="pencil" class="h-4 w-4"></i>
                    Edit
                </a>
                <form action="{{ route('tickets.destroy', $ticket->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus tiket ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-red-200 px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50">
                        <i data-lucide="trash-2" class="h-4 w-4"></i>
                        Hapus
                    </button>
                </form>
            </div>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_320px]">
            <section class="space-y-5 rounded-2xl border border-border bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-center gap-2 border-b border-border pb-4">
                    <x-priority-badge :priority="$ticket->priority" />
                    <x-status-badge :status="$ticket->status" />
                    <span class="ml-auto text-xs text-secondary">
                        Dibuat {{ $ticket->created_at?->format('d M Y H:i') ?? '-' }}
                    </span>
                </div>

                <div>
                    <h2 class="text-lg font-bold text-dark">{{ $ticket->title }}</h2>
                    <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-secondary">{{ $ticket->description ?: 'Tidak ada deskripsi.' }}</p>
                </div>

                <dl class="grid gap-4 rounded-xl border border-border bg-surface p-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-xs text-secondary">Customer</dt>
                        <dd class="mt-1 font-medium text-dark">{{ $ticket->customer?->name ?? $ticket->customer_name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-secondary">Lokasi</dt>
                        <dd class="mt-1 font-medium text-dark">{{ $ticket->location ?? $ticket->customer?->area ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-secondary">Kategori Gangguan</dt>
                        <dd class="mt-1 font-medium text-dark">{{ $ticket->issue_category ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-secondary">Teknisi</dt>
                        <dd class="mt-1 font-medium text-dark">{{ $ticket->activeTechnicians->first()?->name ?? $ticket->assigned_tech_name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-secondary">Dibuat oleh</dt>
                        <dd class="mt-1 font-medium text-dark">{{ $ticket->creator?->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-secondary">Aset</dt>
                        <dd class="mt-1 font-medium text-dark">{{ $ticket->asset?->name ?? '-' }}</dd>
                        @if ($ticket->asset?->serial_number)
                            <dd class="text-xs text-secondary">SN: {{ $ticket->asset->serial_number }}</dd>
                        @endif
                    </div>
                    <div>
                        <dt class="text-xs text-secondary">Batas SLA</dt>
                        <dd class="mt-1 font-medium text-dark">{{ $ticket->sla_due_at?->format('d M Y H:i') ?? '-' }}</dd>
                    </div>
                </dl>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <h3 class="mb-3 font-semibold text-dark">Material</h3>
                        @forelse ($ticket->materials as $material)
                            <div class="flex justify-between border-b border-border py-2 text-sm">
                                <span>{{ $material->item_name ?? '-' }}</span>
                                <span class="text-secondary">{{ $material->quantity }} {{ $material->unit }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-secondary">Belum ada material digunakan.</p>
                        @endforelse
                    </div>
                    <div>
                        <h3 class="mb-3 font-semibold text-dark">Lampiran</h3>
                        @forelse ($ticket->attachments as $attachment)
                            <div class="flex items-center justify-between gap-3 border-b border-border py-2 text-sm">
                                <span class="truncate">{{ $attachment->file_name }}</span>
                                <span class="shrink-0 text-xs text-secondary">{{ $attachment->file_size_human ?? '' }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-secondary">Tidak ada lampiran.</p>
                        @endforelse
                    </div>
                </div>

                <div>
                    <h3 class="mb-3 font-semibold text-dark">Riwayat Aktivitas</h3>
                    @forelse ($activities as $activity)
                        <div class="border-l-2 border-primary py-1 pl-4 text-sm">
                            <p class="font-medium text-dark">{{ $activity->description }}</p>
                            <p class="mt-1 text-xs text-secondary">
                                {{ $activity->user_name }} · {{ $activity->created_at?->format('d M Y H:i') ?? '-' }}
                            </p>
                        </div>
                    @empty
                        <p class="text-sm text-secondary">Belum ada riwayat aktivitas.</p>
                    @endforelse
                </div>
            </section>

            <aside class="h-fit rounded-2xl border border-border bg-white p-5 shadow-sm">
                @if ($ticket->customer && in_array($ticket->status, ['Open', 'Assigned', 'In Progress'], true))
                    <section class="mb-6 border-b border-border pb-6">
                        <h2 class="mb-4 font-semibold text-dark">Assignment Teknisi</h2>
                        <form id="ticket-assignment-form"
                              action="{{ route('tickets.assignments.store', $ticket->id) }}"
                              method="POST"
                              class="space-y-3">
                            @csrf
                            <label for="technician_id" class="block text-xs font-medium text-secondary">Teknisi</label>
                            <select id="technician_id" name="technician_id" required class="w-full rounded-xl border border-border bg-white px-3 py-2 text-sm">
                                <option value="">Pilih teknisi</option>
                                @foreach ($technicians as $technician)
                                    <option value="{{ $technician->id }}" @selected($ticket->activeTechnicians->contains('id', $technician->id))>
                                        {{ $technician->name }} ({{ $technician->status }})
                                    </option>
                                @endforeach
                            </select>
                            <div id="ticket-assignment-message" class="hidden rounded-xl px-3 py-2 text-sm" role="status" aria-live="polite"></div>
                            <button type="submit" class="w-full rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60">
                                Tugaskan Teknisi
                            </button>
                        </form>
                    </section>
                @endif

                <h2 class="mb-4 font-semibold text-dark">Ubah Status</h2>
                <form action="{{ route('tickets.update-status', $ticket->id) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PATCH')
                    <label for="status" class="block text-xs font-medium text-secondary">Status tiket</label>
                    <select id="status" name="status" class="w-full rounded-xl border border-border bg-white px-3 py-2 text-sm">
                        @foreach (['Open', 'Assigned', 'In Progress', 'Waiting', 'Resolved', 'Closed'] as $status)
                            <option value="{{ $status }}" @selected(old('status', $ticket->status) === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                        Simpan Status
                    </button>
                </form>
            </aside>
        </div>
    </div>

    <script>
        const assignmentForm = document.getElementById('ticket-assignment-form');
        if (assignmentForm) {
            assignmentForm.addEventListener('submit', async event => {
                event.preventDefault();
                const button = assignmentForm.querySelector('button[type="submit"]');
                const message = document.getElementById('ticket-assignment-message');
                button.disabled = true;
                message.classList.add('hidden');

                try {
                    const response = await fetch(assignmentForm.action, {
                        method: 'POST',
                        body: new FormData(assignmentForm),
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': assignmentForm.querySelector('input[name="_token"]').value,
                        },
                    });
                    const data = await response.json();

                    if (!response.ok) {
                        const errors = Object.values(data.errors || {}).flat();
                        throw new Error(errors.join(' ') || data.message || 'Assignment teknisi gagal disimpan.');
                    }

                    message.textContent = data.message || 'Teknisi berhasil ditugaskan.';
                    message.className = 'rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700';
                    window.location.reload();
                } catch (error) {
                    message.textContent = error.message || 'Tidak dapat menyimpan assignment teknisi.';
                    message.className = 'rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700';
                    button.disabled = false;
                }
            });
        }
    </script>
</x-app-layout>
