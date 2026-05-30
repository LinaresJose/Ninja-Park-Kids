@props(['icon' => null, 'type' => 'button', 'outline' => false])

<button type="{{ $type }}" {{ $attributes->merge(['class' => $outline ? 'btn-ninja-outline' : 'btn-ninja']) }}>
    @if($icon)
        <i class="bi bi-{{ $icon }} me-2"></i>
    @endif
    {{ $slot }}
</button>
