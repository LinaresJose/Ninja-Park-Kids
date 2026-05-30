@props(['id', 'name', 'label', 'icon', 'type' => 'text', 'placeholder' => ''])

<div class="mb-4">
    <label for="{{ $id }}" class="form-label" style="font-weight: 700; color: var(--ninja-purple);">{{ $label }}</label>
    <div class="input-group">
        <span class="input-group-text" style="background: white; border-right: none; border-radius: 12px 0 0 12px;">
            <i class="bi bi-{{ $icon }} text-muted"></i>
        </span>
        <input type="{{ $type }}" name="{{ $name }}" id="{{ $id }}" class="form-control" style="border-left: none; border-radius: 0 12px 12px 0;" placeholder="{{ $placeholder }}" {{ $attributes }}>
    </div>
</div>
