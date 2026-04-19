<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen antialiased">

    {{-- Fondo con imagen a pantalla completa --}}
    <div
        class="fixed inset-0 bg-cover bg-center bg-no-repeat scale-110 filter blur-[3px]"
        style="background-image: url('{{ asset('images/fondoUrbans.png') }}');">
        <div class="absolute inset-0 bg-black/50"></div>
    </div>

    {{-- Contenido centrado sobre el fondo --}}
    <div class="relative min-h-screen flex flex-col items-center justify-center p-6">
        <div class="w-full max-w-sm bg-white rounded-2xl shadow-2xl px-8 py-10 flex flex-col gap-6">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 scale-120" wire:navigate>
                <img
                    src="{{ asset('images/logourvans.png') }}"
                    alt="{{ config('app.name', 'Laravel') }}"
                    class="h-24 w-auto object-contain">
                <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
            </a>

            {{-- Slot: contenido del login --}}
            <div class="flex flex-col gap-6">
                {{ $slot }}
            </div>

        </div>
    </div>

    @fluxScripts

</body>

</html>