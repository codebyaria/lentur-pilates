<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="theme-color" content="#fff8ee">

        {{-- Situs Lentur hanya punya tema terang (putih hangat). --}}
        <style>
            html {
                background-color: #fff8ee;
            }
        </style>

        {{-- Pratinjau tautan: dirender server karena crawler (WhatsApp, LinkedIn) tidak menjalankan JS. --}}
        @php
            $locale = app()->getLocale() === 'en' ? 'en' : 'id';
            $metaTitle = __('Lentur Pilates · Gerak dulu. Sempurna belakangan.');
            $metaDescription = __('Studio pilates mat & reformer dengan kelas kecil, untuk semua bentuk tubuh. Booking online, batal gratis sampai :hours jam sebelum kelas.', ['hours' => config('lentur.free_cancellation_hours')]);
        @endphp
        <meta name="description" content="{{ $metaDescription }}">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="Lentur Pilates">
        <meta property="og:title" content="{{ $metaTitle }}">
        <meta property="og:description" content="{{ $metaDescription }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:locale" content="{{ $locale === 'en' ? 'en_US' : 'id_ID' }}">
        <meta property="og:image" content="{{ asset("images/og/lentur-{$locale}.png") }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="{{ __('Kata “lentur” dan enam orang dengan bentuk tubuh berbeda, termasuk pengguna kursi roda.') }}">
        <meta name="twitter:card" content="summary_large_image">

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @fonts

        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/js/app.tsx', "resources/js/pages/{$page['component']}.tsx"])
        <x-inertia::head>
            <title>{{ config('app.name', 'Laravel') }}</title>
        </x-inertia::head>
    </head>
    <body class="font-sans antialiased">
        <x-inertia::app />
    </body>
</html>
