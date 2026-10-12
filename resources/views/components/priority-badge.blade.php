@props(['priority'])

@php
    $priorityClasses = [
        'Low'      => 'bg-emerald-50 text-emerald-600 border border-emerald-200/60',
        'Normal'   => 'bg-slate-100 text-slate-600 border border-slate-200/60',
        'High'     => 'bg-red-50 text-red-600 border border-red-200/60 font-semibold',
        'Critical' => 'bg-rose-100 text-rose-700 border border-rose-300 font-bold',
    ];

    $classes = $priorityClasses[$priority] ?? 'bg-gray-100 text-gray-600 border border-gray-200';
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium whitespace-nowrap {{ $classes }}">
    {{ $priority }}
</span>
