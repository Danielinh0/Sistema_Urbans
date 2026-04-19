<x-layouts::auth :title="__('Iniciar Sesión')">
    <div class="flex flex-col gap-6">

        {{-- título --}}
        <div class="flex flex-col items-center gap-3">
            <h1 class="text-2xl font-bold text-blue-800 tracking-wide">
                Bienvenido
            </h1>
        </div>

        {{-- Session Status --}}
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
            @csrf

            {{-- Usuario / Email --}}
            <flux:input
                name="email"
                :label="__('Usuario')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="correo@ejemplo.com" />

            {{-- Contraseña --}}
            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Contraseña')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('••••••••')"
                    viewable />

                @if (Route::has('password.request'))
                <flux:link
                    class="absolute top-0 end-0 text-xs text-blue-800 hover:text-blue-900 transition-colors"
                    :href="route('password.request')"
                    wire:navigate>
                    {{ __('¿Olvidaste tu contraseña?') }}
                </flux:link>
                @endif
            </div>

            {{-- Recuérdame --}}
            <flux:checkbox
                name="remember"
                :label="__('Recuérdame')"
                :checked="old('remember')" />

            {{-- Botón Entrar --}}
            <flux:button
                variant="primary"
                type="submit"
                class="w-full mt-1 bg-blue-800 hover:bg-blue-900 text-white font-semibold py-2 rounded-lg transition-colors"
                data-test="login-button">
                {{ __('Entrar') }}
            </flux:button>
        </form>

        {{-- Registro --}}
        @if (Route::has('register'))
        <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-500">
            <span>{{ __('¿No tienes cuenta?') }}</span>
            <flux:link :href="route('register')" wire:navigate class="text-blue-800 hover:text-blue-900">
                {{ __('Regístrate') }}
            </flux:link>
        </div>
        @endif

    </div>
</x-layouts::auth>