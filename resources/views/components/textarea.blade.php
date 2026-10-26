@props([
    'name',
    'label' => null,
    'id' => null,
    'hint' => null,
    'value' => null,
    'required' => false,
    'rows' => 4,
    'wrapperClass' => null,
])

@php
    $id ??= $name;
    $error = isset($errors) ? $errors->first($name) : null;
    $describedBy = $error ? $id.'-error' : ($hint ? $id.'-hint' : null);
@endphp

<x-field :label="$label" :for="$id" :hint="$hint" :error="$error" :required="$required" :class="$wrapperClass">
    <textarea
        id="{{ $id }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @required($required)
        @if ($error) aria-invalid="true" @endif
        @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
        {{ $attributes->class('form-control') }}
    >{{ old($name, $value) }}</textarea>
</x-field>
