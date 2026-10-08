@props(['status'])

@php
    $enum = \App\Enums\PengajuanStatus::tryFrom($status);
    $class = $enum?->badgeClass() ?? 'bg-slate-100 text-slate-700';
    $label = $enum?->value ?? $status;
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {$class}"]) }}>
    {{ $label }}
</span>
