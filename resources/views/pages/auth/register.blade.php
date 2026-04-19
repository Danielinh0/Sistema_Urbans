<x-layouts::auth :title="__('Registro')">
    <div class="flex flex-col gap-6">

        {{-- Título --}}
        <div class="flex flex-col items-center text-center gap-1">
            <h1 class="text-2xl font-bold text-blue-800">Crear cuenta</h1>
            <p class="text-sm text-zinc-500">Ingresa tus datos para registrarte</p>
        </div>

        {{-- Session Status --}}
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-5">
            @csrf

            {{-- Nombre --}}
            <flux:input
                name="name"
                :label="__('Nombre')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Nombre completo')" />

            {{-- Correo --}}
            <flux:input
                name="email"
                :label="__('Correo electrónico')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="correo@ejemplo.com" />

            {{-- Contraseña --}}
            <flux:input
                name="password"
                :label="__('Contraseña')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('••••••••')"
                viewable />

            {{-- Confirmar Contraseña --}}
            <flux:input
                name="password_confirmation"
                :label="__('Confirmar contraseña')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('••••••••')"
                viewable />

            {{-- Botón --}}
            <flux:button
                type="submit"
                variant="primary"
                class="w-full mt-1 bg-blue-800 hover:bg-blue-900 text-white font-semibold py-2 rounded-lg transition-colors"
                data-test="register-user-button">
                {{ __('Crear cuenta') }}
            </flux:button>

        </form>

        {{-- Ya tienes cuenta --}}
        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-500">
            <span>{{ __('¿Ya tienes cuenta?') }}</span>
            <flux:link :href="route('login')" wire:navigate class="text-blue-800 hover:text-blue-900">
                {{ __('Inicia sesión') }}
            </flux:link>
        </div>

    </div>
</x-layouts::auth>