@props([
    'icon' => 'bi-graph-up',
    'color' => 'blue',
    'label',
    'value',
    'sublabel' => null,
    'href' => null,
])

@php $Tag = $href ? 'a' : 'div'; @endphp

<{{ $Tag }} @if($href) href="{{ $href }}" @endif class="stat-tile">
    <div class="card card-brand h-100">
        <div class="card-body p-3 d-flex align-items-center gap-3">
            <div class="stat-icon stat-icon-{{ $color }}">
                <i class="bi {{ $icon }}"></i>
            </div>
            <div class="flex-grow-1">
                <div class="text-muted small mb-1">{{ $label }}</div>
                <div class="font-display fs-4 lh-1">{{ $value }}</div>
                @if ($sublabel)
                    <div class="text-muted small mt-1">{{ $sublabel }}</div>
                @endif
            </div>
        </div>
    </div>
</{{ $Tag }}>
