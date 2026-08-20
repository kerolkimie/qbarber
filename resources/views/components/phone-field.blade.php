@props([
    'name' => 'phone',
    'label' => 'No. Telefon',
    'required' => false,
    'value' => null,
])

@php
    // Utamakan old() (lepas validation gagal), kalau tiada guna $value (nilai
    // sedia ada dari database — PENTING untuk borang EDIT supaya field tak
    // kosong bila page pertama kali dibuka).
    $oldFull = old($name, $value ?? '');
    $localPart = $oldFull ? ltrim(preg_replace('/^\+?60/', '', $oldFull), '0') : '';
@endphp

<label for="{{ $name }}_local" class="form-label fw-semibold">{{ $label }}</label>
<div class="input-group mb-3">
    <span class="input-group-text">+60</span>
    <input
        type="text"
        id="{{ $name }}_local"
        class="form-control phone-local-input"
        data-hidden-target="{{ $name }}"
        placeholder="12-3456789"
        value="{{ $localPart }}"
        @if ($required) required @endif
    >
</div>
<input type="hidden" name="{{ $name }}" id="{{ $name }}" value="{{ $oldFull }}">
