@php
    $pageTitle = ($title ?? null) ? $title.' | '.config('petshop.name') : config('petshop.name');
    $pageDescription = ($description ?? null) ?: __('ui.home.description');
@endphp

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $pageDescription }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ config('petshop.name') }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $pageDescription }}">
<meta property="og:url" content="{{ $canonical ?? url()->current() }}">
@if ($image ?? null)
    <meta property="og:image" content="{{ $image }}">
@endif
<meta name="theme-color" content="#1F5035">
<link rel="canonical" href="{{ $canonical ?? url()->current() }}">

<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600&family=Nunito:wght@700;800;900&display=swap" rel="stylesheet">

@livewireStyles
@vite(['resources/css/app.css', 'resources/js/app.js'])
