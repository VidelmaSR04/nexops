@props(['status'])

@php
    $statusClasses = [
        'Aktif'   => 'bg-emerald-50 text-emerald-600 border border-emerald-200/60',
        'Baru'    => 'bg-blue-50 text-blue-600 border border-blue-200/60',
        'Nonaktif'=> 'bg-red-50 text-red-600 border border-red-200/60',
    ];

    $classes = $statusClasses[$status] ?? 'bg-gray-100 text-gray-600 border border-gray-200';
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium whitespace-nowrap {{ $classes }}">
    {{ $status }}
</span>