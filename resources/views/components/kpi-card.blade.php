@props([
    'title',
    'value',
    'icon',
    'color' => 'primary',
    'subtitle' => null,
    'trend' => null,
    'trendUp' => true,
])

@php
    $colorMap = [
        'primary' => ['bg' => 'bg-blue-50', 'icon' => 'text-primary', 'iconBg' => 'bg-blue-100'],
        'info'    => ['bg' => 'bg-cyan-50', 'icon' => 'text-info', 'iconBg' => 'bg-cyan-100'],
        'danger'  => ['bg' => 'bg-red-50', 'icon' => 'text-danger', 'iconBg' => 'bg-red-100'],
        'warning' => ['bg' => 'bg-amber-50', 'icon' => 'text-warning', 'iconBg' => 'bg-amber-100'],
        'success' => ['bg' => 'bg-emerald-50', 'icon' => 'text-success', 'iconBg' => 'bg-emerald-100'],
    ];
    $c = $colorMap[$color] ?? $colorMap['primary'];
@endphp

<div class="bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $title }}</p>
            <p class="text-3xl font-bold text-dark mt-1">{{ $value }}</p>

            @if($trend)
                <div class="flex items-center gap-1 mt-2">
                    @if($trendUp)
                        <i data-lucide="trending-up" class="w-3.5 h-3.5 text-success"></i>
                        <span class="text-xs font-medium text-success">{{ $trend }}</span>
                    @else
                        <i data-lucide="trending-down" class="w-3.5 h-3.5 text-danger"></i>
                        <span class="text-xs font-medium text-danger">{{ $trend }}</span>
                    @endif
                    @if($subtitle)
                        <span class="text-xs text-gray-400">{{ $subtitle }}</span>
                    @endif
                </div>
            @elseif($subtitle)
                <p class="text-xs text-gray-400 mt-2">{{ $subtitle }}</p>
            @endif
        </div>

        <div class="{{ $c['iconBg'] }} rounded-xl p-2.5">
            <i data-lucide="{{ $icon }}" class="w-5 h-5 {{ $c['icon'] }}"></i>
        </div>
    </div>
</div>
