@props(['status'])

@php
    $statusClasses = [
        'Open'        => 'bg-blue-50 text-blue-600 border border-blue-200/60',
        'Assigned'    => 'bg-purple-50 text-purple-600 border border-purple-200/60',
        'In Progress' => 'bg-amber-50 text-amber-600 border border-amber-200/60',
        'Waiting'     => 'bg-orange-50 text-orange-600 border border-orange-200/60',
        'Resolved'    => 'bg-emerald-50 text-emerald-600 border border-emerald-200/60',
        'Closed'      => 'bg-slate-100 text-slate-500 border border-slate-200/60',
    ];

    $classes = $statusClasses[$status] ?? 'bg-gray-100 text-gray-600 border border-gray-200';
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium whitespace-nowrap {{ $classes }}">
    {{ $status }}
</span>
