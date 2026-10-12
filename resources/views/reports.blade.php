<x-app-layout>
    <div class="space-y-6">

        {{-- 1. HEADER REPORTS --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-dark tracking-tight">Reports</h1>
                <p class="text-secondary text-sm mt-0.5">Lihat laporan dan analisis data operasional</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <select id="report-period-select" class="text-xs rounded-xl border border-border bg-white px-3 py-2 text-dark focus:outline-none focus:ring-2 focus:ring-primary/20">
                    <option value="7">7 Hari Terakhir</option>
                    <option value="30" selected>30 Hari Terakhir</option>
                    <option value="month">Bulan Ini</option>
                    <option value="prev_month">Bulan Sebelumnya</option>
                    <option value="custom">Rentang Tanggal Khusus</option>
                </select>

                <button type="button" id="btn-download-report"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white text-xs font-medium hover:bg-blue-700 shadow-sm transition-colors cursor-pointer">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span>Download Laporan</span>
                </button>
            </div>
        </div>

        {{-- 2. KARTU STATISTIK --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i data-lucide="ticket" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Total Ticket</p>
                    <div class="flex items-baseline gap-2 mt-0.5">
                        <p class="text-2xl font-bold text-dark">248</p>
                        <span class="text-[10px] font-semibold text-emerald-600 flex items-center"><i data-lucide="arrow-up-right" class="w-3 h-3"></i> +12%</span>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-0.5">vs periode sebelumnya</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Ticket Selesai</p>
                    <div class="flex items-baseline gap-2 mt-0.5">
                        <p class="text-2xl font-bold text-dark">198</p>
                        <span class="text-[10px] font-semibold text-emerald-600 flex items-center"><i data-lucide="arrow-up-right" class="w-3 h-3"></i> +8%</span>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-0.5">vs periode sebelumnya</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Ticket Pending</p>
                    <div class="flex items-baseline gap-2 mt-0.5">
                        <p class="text-2xl font-bold text-dark">42</p>
                        <span class="text-[10px] font-semibold text-red-600 flex items-center"><i data-lucide="arrow-down-right" class="w-3 h-3"></i> -3%</span>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-0.5">vs periode sebelumnya</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-border p-4.5 flex items-center gap-3.5 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="w-11 h-11 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs text-secondary font-medium">Ticket Overdue</p>
                    <div class="flex items-baseline gap-2 mt-0.5">
                        <p class="text-2xl font-bold text-dark">8</p>
                        <span class="text-[10px] font-semibold text-emerald-600 flex items-center"><i data-lucide="arrow-down-right" class="w-3 h-3"></i> -2%</span>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-0.5">vs periode sebelumnya</p>
                </div>
            </div>
        </div>

        {{-- 3. GRAFIK TREN TICKET --}}
        <div class="bg-white rounded-2xl border border-border p-5 shadow-[0_2px_6px_rgba(0,0,0,0.02)]">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                <div>
                    <h3 class="text-base font-bold text-dark">Tren Ticket</h3>
                    <p class="text-secondary text-xs">Perbandingan ticket masuk dan selesai harian</p>
                </div>
                <div class="flex items-center gap-4 text-xs font-medium">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-primary inline-block"></span>
                        <span class="text-dark">Ticket Masuk</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                        <span class="text-dark">Ticket Selesai</span>
                    </div>
                </div>
            </div>

            <div class="relative w-full h-64 bg-surface rounded-xl border border-border/60 p-4 flex items-end justify-between gap-2 overflow-x-auto">
                <div class="absolute inset-x-4 top-6 border-b border-gray-200/60"></div>
                <div class="absolute inset-x-4 top-16 border-b border-gray-200/60"></div>
                <div class="absolute inset-x-4 top-28 border-b border-gray-200/60"></div>
                <div class="absolute inset-x-4 top-38 border-b border-gray-200/60"></div>

                <div class="relative z-10 flex-1 flex flex-col items-center gap-2 h-full justify-end group">
                    <div class="flex items-end gap-1.5 w-full justify-center h-48">
                        <div style="height: 40%" class="w-3 bg-primary rounded-t-md"></div>
                        <div style="height: 35%" class="w-3 bg-emerald-500 rounded-t-md"></div>
                    </div>
                    <span class="text-[10px] text-secondary font-medium">01 Okt</span>
                </div>
                <div class="relative z-10 flex-1 flex flex-col items-center gap-2 h-full justify-end group">
                    <div class="flex items-end gap-1.5 w-full justify-center h-48">
                        <div style="height: 60%" class="w-3 bg-primary rounded-t-md"></div>
                        <div style="height: 50%" class="w-3 bg-emerald-500 rounded-t-md"></div>
                    </div>
                    <span class="text-[10px] text-secondary font-medium">05 Okt</span>
                </div>
                <div class="relative z-10 flex-1 flex flex-col items-center gap-2 h-full justify-end group">
                    <div class="flex items-end gap-1.5 w-full justify-center h-48">
                        <div style="height: 45%" class="w-3 bg-primary rounded-t-md"></div>
                        <div style="height: 45%" class="w-3 bg-emerald-500 rounded-t-md"></div>
                    </div>
                    <span class="text-[10px] text-secondary font-medium">10 Okt</span>
                </div>
                <div class="relative z-10 flex-1 flex flex-col items-center gap-2 h-full justify-end group">
                    <div class="flex items-end gap-1.5 w-full justify-center h-48">
                        <div style="height: 75%" class="w-3 bg-primary rounded-t-md"></div>
                        <div style="height: 70%" class="w-3 bg-emerald-500 rounded-t-md"></div>
                    </div>
                    <span class="text-[10px] text-secondary font-medium">15 Okt</span>
                </div>
                <div class="relative z-10 flex-1 flex flex-col items-center gap-2 h-full justify-end group">
                    <div class="flex items-end gap-1.5 w-full justify-center h-48">
                        <div style="height: 50%" class="w-3 bg-primary rounded-t-md"></div>
                        <div style="height: 55%" class="w-3 bg-emerald-500 rounded-t-md"></div>
                    </div>
                    <span class="text-[10px] text-secondary font-medium">20 Okt</span>
                </div>
                <div class="relative z-10 flex-1 flex flex-col items-center gap-2 h-full justify-end group">
                    <div class="flex items-end gap-1.5 w-full justify-center h-48">
                        <div style="height: 70%" class="w-3 bg-primary rounded-t-md"></div>
                        <div style="height: 60%" class="w-3 bg-emerald-500 rounded-t-md"></div>
                    </div>
                    <span class="text-[10px] text-secondary font-medium">25 Okt</span>
                </div>
                <div class="relative z-10 flex-1 flex flex-col items-center gap-2 h-full justify-end group">
                    <div class="flex items-end gap-1.5 w-full justify-center h-48">
                        <div style="height: 40%" class="w-3 bg-primary rounded-t-md"></div>
                        <div style="height: 45%" class="w-3 bg-emerald-500 rounded-t-md"></div>
                    </div>
                    <span class="text-[10px] text-secondary font-medium">30 Okt</span>
                </div>
            </div>
        </div>

        {{-- 4. GRID GRAFIK (LOKASI & KATEGORI) --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div class="bg-white rounded-2xl border border-border p-5 shadow-[0_2px_6px_rgba(0,0,0,0.02)] flex flex-col justify-between">
                <div>
                    <h3 class="text-base font-bold text-dark">Ticket per Lokasi</h3>
                    <p class="text-secondary text-xs">Distribusi ticket berdasarkan wilayah operasional</p>
                </div>
                <div class="flex flex-col sm:flex-row items-center justify-around gap-6 my-6">
                    <div class="relative w-40 h-40 flex items-center justify-center">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-gray-100" stroke-width="4" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-primary" stroke-dasharray="42, 100" stroke-width="4" stroke-dashoffset="0" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-emerald-500" stroke-dasharray="32, 100" stroke-width="4" stroke-dashoffset="-42" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-amber-500" stroke-dasharray="26, 100" stroke-width="4" stroke-dashoffset="-74" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                            <span class="text-xs text-secondary font-medium">Total</span>
                            <strong class="text-lg font-bold text-dark">248</strong>
                        </div>
                    </div>

                    <div class="space-y-2.5 text-xs w-full sm:w-auto">
                        <div class="flex items-center justify-between sm:justify-start gap-4">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-primary inline-block"></span>
                                <span class="text-dark font-medium">Situbondo</span>
                            </div>
                            <span class="text-secondary font-semibold">42% (104)</span>
                        </div>
                        <div class="flex items-center justify-between sm:justify-start gap-4">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                                <span class="text-dark font-medium">Besuki</span>
                            </div>
                            <span class="text-secondary font-semibold">32% (79)</span>
                        </div>
                        <div class="flex items-center justify-between sm:justify-start gap-4">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-amber-500 inline-block"></span>
                                <span class="text-dark font-medium">Lapangan</span>
                            </div>
                            <span class="text-secondary font-semibold">26% (65)</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-border p-5 shadow-[0_2px_6px_rgba(0,0,0,0.02)] flex flex-col justify-between">
                <div>
                    <h3 class="text-base font-bold text-dark">Ticket per Kategori</h3>
                    <p class="text-secondary text-xs">Kategori masalah yang paling sering terjadi</p>
                </div>
                <div class="space-y-3 my-4 text-xs">
                    <div>
                        <div class="flex justify-between font-medium text-dark mb-1">
                            <span>Internet</span>
                            <span>86 ticket</span>
                        </div>
                        <div class="w-full h-2.5 bg-surface rounded-full overflow-hidden border border-border/60">
                            <div class="bg-primary h-full rounded-full" style="width: 86%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between font-medium text-dark mb-1">
                            <span>Perangkat</span>
                            <span>62 ticket</span>
                        </div>
                        <div class="w-full h-2.5 bg-surface rounded-full overflow-hidden border border-border/60">
                            <div class="bg-emerald-500 h-full rounded-full" style="width: 62%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between font-medium text-dark mb-1">
                            <span>Jaringan</span>
                            <span>48 ticket</span>
                        </div>
                        <div class="w-full h-2.5 bg-surface rounded-full overflow-hidden border border-border/60">
                            <div class="bg-amber-500 h-full rounded-full" style="width: 48%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between font-medium text-dark mb-1">
                            <span>Power</span>
                            <span>32 ticket</span>
                        </div>
                        <div class="w-full h-2.5 bg-surface rounded-full overflow-hidden border border-border/60">
                            <div class="bg-cyan-500 h-full rounded-full" style="width: 32%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between font-medium text-dark mb-1">
                            <span>Lainnya</span>
                            <span>20 ticket</span>
                        </div>
                        <div class="w-full h-2.5 bg-surface rounded-full overflow-hidden border border-border/60">
                            <div class="bg-gray-400 h-full rounded-full" style="width: 20%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. TABEL TEKNISI & STATUS TICKET --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
            <div class="xl:col-span-2 bg-white rounded-2xl border border-border p-5 shadow-[0_2px_6px_rgba(0,0,0,0.02)]">
                <div class="mb-4">
                    <h3 class="text-base font-bold text-dark">Performa Teknisi</h3>
                    <p class="text-secondary text-xs">Top 5 teknisi dengan penyelesaian ticket terbanyak</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr class="text-secondary bg-[#FAFCFF] border-b border-border font-medium text-[11px] uppercase tracking-wider">
                                <th class="py-3 pl-3 w-12 text-center">Peringkat</th>
                                <th class="py-3 pr-3">Nama Teknisi</th>
                                <th class="py-3 pr-3 text-center">Ticket Selesai</th>
                                <th class="py-3 pr-3 text-center">Rating</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr class="hover:bg-blue-50/40 transition-colors">
                                <td class="py-3 pl-3 text-center font-bold text-primary">#1</td>
                                <td class="py-3 pr-3">
                                    <div class="flex items-center gap-2.5">
                                        <img src="https://ui-avatars.com/api/?name=Budi+Santoso&background=0D6EFD&color=fff" class="w-7 h-7 rounded-full shrink-0">
                                        <span class="font-semibold text-dark">Budi Santoso</span>
                                    </div>
                                </td>
                                <td class="py-3 pr-3 text-center font-bold text-dark">28</td>
                                <td class="py-3 pr-3 text-center">
                                    <div class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 px-2 py-0.5 rounded-full font-semibold">
                                        <i data-lucide="star" class="w-3 h-3 fill-amber-400 text-amber-400"></i>
                                        <span>4.8</span>
                                    </div>
                                </td>
                            </tr>
                            <tr class="hover:bg-blue-50/40 transition-colors">
                                <td class="py-3 pl-3 text-center font-bold text-primary">#2</td>
                                <td class="py-3 pr-3">
                                    <div class="flex items-center gap-2.5">
                                        <img src="https://ui-avatars.com/api/?name=Ahmad+Fauzi&background=0D6EFD&color=fff" class="w-7 h-7 rounded-full shrink-0">
                                        <span class="font-semibold text-dark">Ahmad Fauzi</span>
                                    </div>
                                </td>
                                <td class="py-3 pr-3 text-center font-bold text-dark">24</td>
                                <td class="py-3 pr-3 text-center">
                                    <div class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 px-2 py-0.5 rounded-full font-semibold">
                                        <i data-lucide="star" class="w-3 h-3 fill-amber-400 text-amber-400"></i>
                                        <span>4.6</span>
                                    </div>
                                </td>
                            </tr>
                            <tr class="hover:bg-blue-50/40 transition-colors">
                                <td class="py-3 pl-3 text-center font-bold text-primary">#3</td>
                                <td class="py-3 pr-3">
                                    <div class="flex items-center gap-2.5">
                                        <img src="https://ui-avatars.com/api/?name=Dimas+Priyanto&background=0D6EFD&color=fff" class="w-7 h-7 rounded-full shrink-0">
                                        <span class="font-semibold text-dark">Dimas Priyanto</span>
                                    </div>
                                </td>
                                <td class="py-3 pr-3 text-center font-bold text-dark">22</td>
                                <td class="py-3 pr-3 text-center">
                                    <div class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 px-2 py-0.5 rounded-full font-semibold">
                                        <i data-lucide="star" class="w-3 h-3 fill-amber-400 text-amber-400"></i>
                                        <span>4.7</span>
                                    </div>
                                </td>
                            </tr>
                            <tr class="hover:bg-blue-50/40 transition-colors">
                                <td class="py-3 pl-3 text-center font-bold text-primary">#4</td>
                                <td class="py-3 pr-3">
                                    <div class="flex items-center gap-2.5">
                                        <img src="https://ui-avatars.com/api/?name=Rizky+Maulana&background=0D6EFD&color=fff" class="w-7 h-7 rounded-full shrink-0">
                                        <span class="font-semibold text-dark">Rizky Maulana</span>
                                    </div>
                                </td>
                                <td class="py-3 pr-3 text-center font-bold text-dark">18</td>
                                <td class="py-3 pr-3 text-center">
                                    <div class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 px-2 py-0.5 rounded-full font-semibold">
                                        <i data-lucide="star" class="w-3 h-3 fill-amber-400 text-amber-400"></i>
                                        <span>4.5</span>
                                    </div>
                                </td>
                            </tr>
                            <tr class="hover:bg-blue-50/40 transition-colors">
                                <td class="py-3 pl-3 text-center font-bold text-primary">#5</td>
                                <td class="py-3 pr-3">
                                    <div class="flex items-center gap-2.5">
                                        <img src="https://ui-avatars.com/api/?name=Fajar+Ramadhan&background=0D6EFD&color=fff" class="w-7 h-7 rounded-full shrink-0">
                                        <span class="font-semibold text-dark">Fajar Ramadhan</span>
                                    </div>
                                </td>
                                <td class="py-3 pr-3 text-center font-bold text-dark">16</td>
                                <td class="py-3 pr-3 text-center">
                                    <div class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 px-2 py-0.5 rounded-full font-semibold">
                                        <i data-lucide="star" class="w-3 h-3 fill-amber-400 text-amber-400"></i>
                                        <span>4.4</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-border p-5 shadow-[0_2px_6px_rgba(0,0,0,0.02)] flex flex-col justify-between">
                <div>
                    <h3 class="text-base font-bold text-dark">Status Ticket</h3>
                    <p class="text-secondary text-xs">Persentase status ticket keseluruhan</p>
                </div>
                <div class="my-4 flex flex-col items-center justify-center">
                    <div class="relative w-36 h-36 flex items-center justify-center my-2">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-gray-100" stroke-width="4" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-emerald-500" stroke-dasharray="79, 100" stroke-width="4" stroke-dashoffset="0" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-amber-500" stroke-dasharray="17, 100" stroke-width="4" stroke-dashoffset="-79" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-red-500" stroke-dasharray="4, 100" stroke-width="4" stroke-dashoffset="-96" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                            <span class="text-xs text-secondary font-medium">Total</span>
                            <strong class="text-lg font-bold text-dark">248</strong>
                        </div>
                    </div>
                </div>
                <div class="space-y-2 text-xs pt-2 border-t border-border">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                            <span class="text-dark font-medium">Selesai</span>
                        </div>
                        <span class="text-secondary font-semibold">79% (198)</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-amber-500 inline-block"></span>
                            <span class="text-dark font-medium">Pending</span>
                        </div>
                        <span class="text-secondary font-semibold">17% (42)</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span>
                            <span class="text-dark font-medium">Overdue</span>
                        </div>
                        <span class="text-secondary font-semibold">4% (8)</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div id="toast-notification" class="fixed bottom-5 right-5 z-50 hidden max-w-sm rounded-xl bg-dark text-white text-xs px-4 py-3 shadow-xl transition-all"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let toastTimer;
            function showToast(message) {
                const toast = document.getElementById('toast-notification');
                toast.textContent = message;
                toast.classList.remove('hidden');
                clearTimeout(toastTimer);
                toastTimer = setTimeout(() => toast.classList.add('hidden'), 2500);
            }

            document.getElementById('report-period-select').addEventListener('change', function(e) {
                const val = e.target.options[e.target.selectedIndex].text;
                showToast(`Periode laporan diperbarui ke: ${val}`);
            });

            document.getElementById('btn-download-report').addEventListener('click', function() {
                const csvContent = "data:text/csv;charset=utf-8,ID Ticket,Kategori,Lokasi,Status,Teknisi\nTK-1001,Internet,Situbondo,Selesai,Budi Santoso\nTK-1002,Perangkat,Besuki,Pending,Ahmad Fauzi";
                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", "laporan-operasional-nexops.csv");
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                showToast("Berhasil mengunduh laporan-operasional-nexops.csv");
            });

            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</x-app-layout>
