@props([
    'icon' => 'fas fa-inbox',
    'message' => 'No records found.',
    'actionUrl' => null,
    'actionLabel' => null,
])

<div class="empty-state">
    <i class="{{ $icon }}"></i>
    <p class="mb-2">{{ $message }}</p>
    @if ($actionUrl && $actionLabel)
        <a href="{{ $actionUrl }}" class="btn btn-sm btn-outline-primary">{{ $actionLabel }}</a>
    @endif
</div>
