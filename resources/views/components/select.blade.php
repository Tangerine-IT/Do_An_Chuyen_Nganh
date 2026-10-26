@props([
    'name',
    'label' => null,
    'id' => null,
    'hint' => null,
    'required' => false,
    'wrapperClass' => null,
    'options' => [],
    'value' => null,
])

@php
    $id ??= $name;
    $error = isset($errors) ? $errors->first($name) : null;
    $describedBy = $error ? $id.'-error' : ($hint ? $id.'-hint' : null);
@endphp

<x-field :label="$label" :for="$id" :hint="$hint" :error="$error" :required="$required" :class="$wrapperClass">
    <select
        id="{{ $id }}"
        name="{{ $name }}"
        @required($required)
        @if ($error) aria-invalid="true" @endif
        @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
        {{ $attributes->class('form-control pe-10') }}
    >
        @if ($slot->isNotEmpty())
            {{ $slot }}
        @else
            @foreach ($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" @selected((string) old($name, $value) === (string) $optionValue)>{{ $optionLabel }}</option>
            @endforeach
        @endif
    </select>
</x-field>
