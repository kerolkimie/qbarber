@props([
    'name' => 'password',
    'label' => 'Kata Laluan',
    'autocomplete' => 'new-password',
    'required' => true,
    'hint' => null,
])

<label for="{{ $name }}" class="form-label fw-semibold">{{ $label }}</label>
<div class="input-group mb-1">
    <input
        type="password"
        name="{{ $name }}"
        id="{{ $name }}"
        class="form-control"
        autocomplete="{{ $autocomplete }}"
        @if ($required) required @endif
    >
    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="{{ $name }}" tabindex="-1" aria-label="Papar/sembunyi kata laluan">
        <i class="bi bi-eye" id="icon-{{ $name }}"></i>
    </button>
</div>
@if ($hint)
    <p class="text-muted small mb-3">{{ $hint }}</p>
@else
    <div class="mb-3"></div>
@endif
