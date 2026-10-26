@props([
    'name',
    'label' => null,
    'id' => null,
    'type' => 'text',
    'hint' => null,
    'value' => null,
    'required' => false,
    'wrapperClass' => null,
])

@php
    $id ??= $name;
    $error = isset($errors) ? $errors->first($name) : null;
    $describedBy = $error ? $id.'-error' : ($hint ? $id.'-hint' : null);
@endphp

<x-field :label="$label" :for="$id" :hint="$hint" :error="$error" :required="$required" :class="$wrapperClass">
    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @if (! in_array($type, ['password', 'file'], true)) value="{{ old($name, $value) }}" @endif
        @required($required)
        @if ($error) aria-invalid="true" @endif
        @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
        {{ $attributes->class('form-control') }}
    >
</x-field>
