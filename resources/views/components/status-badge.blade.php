@props(['status'])

@php
    $colors = [
        'available' => 'success',
        'borrowed' => 'warning',
        'reserved' => 'info',
        'archived' => 'secondary',
        'returned' => 'success',
        'overdue' => 'danger',
        'active' => 'success',
        'inactive' => 'secondary',
    ];
    $color = $colors[$status] ?? 'secondary';
@endphp

<span {{ $attributes->merge(['class' => "badge badge-{$color}"]) }}>
    {{ ucfirst($status) }}
</span>
