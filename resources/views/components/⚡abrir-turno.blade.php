<?php

use App\Models\Taquilla;
use App\Models\Turno;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public $id_taquilla  = ""; // ✅ null por default, sin preselección
    public $monto_inicial = null;

    public function updatedIdTaquilla($value)
    {
        $taquilla = Taquilla::find($value);
        $this->monto_inicial = $taquilla?->monto_actual;
    }

    public function guardar()
    {
        $this->validate([
            'id_taquilla'   => 'required|exists:taquilla,id_taquilla',
            'monto_inicial' => 'required|numeric|min:0',
        ], [
            'id_taquilla.required'   => 'Debes seleccionar una taquilla.',
            'monto_inicial.required' => 'Ingresa el monto inicial.',
        ]);

        $existe = Turno::where('id_usuario', Auth::user()->id_usuario)
            ->whereNull('hora_fin')
            ->exists();

        if ($existe) {
            session()->flash('error', 'Ya tienes un turno activo.');
            return redirect()->route('dashboard');
        }

        Turno::create([
            'id_usuario'    => Auth::user()->id_usuario,
            'id_taquilla'   => $this->id_taquilla,
            'monto_inicial' => $this->monto_inicial,
            'fecha'         => now()->toDateString(),
            'hora_inicio'   => now()->toTimeString(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Turno abierto con éxito.');
    }

    public function with()
    {
        return [
            'taquillas' => Taquilla::all(),
        ];
    }
};
?>

<div class="flex items-center justify-center min-h-screen bg-gray-50 dark:bg-neutral-950 px-4">
    <div class="w-full max-w-md">

        {{-- Card con la línea de diseño del proyecto --}}
        <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-700 shadow-sm overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center gap-3 px-6 py-5 border-b border-neutral-100 dark:border-neutral-800">
                <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex-shrink-0">
                    <flux:icon name="tickets" class="size-5 text-blue-600 dark:text-blue-400" />
                </span>
                <div>
                    <h2 class="font-bold text-gray-800 dark:text-white text-base leading-tight">
                        Apertura de Turno
                    </h2>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                        Selecciona tu taquilla e ingresa el monto base
                    </p>
                </div>
            </div>

            {{-- Formulario --}}
            <div class="px-6 py-6 space-y-5">

                @if(session('error'))
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium
                        bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300
                        border border-red-200 dark:border-red-800">
                    <flux:icon name="circle-minus" class="size-4 flex-shrink-0" />
                    {{ session('error') }}
                </div>
                @endif

                <form wire:submit="guardar" class="space-y-5">

                    {{-- Selector de taquilla --}}
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Taquilla disponible
                        </label>
                        <flux:select
                            wire:model.live="id_taquilla"
                            placeholder="Selecciona una taquilla...">
                            @foreach($taquillas as $taquilla)
                            <flux:select.option value="{{ $taquilla->id_taquilla }}">
                                Taquilla #{{ $taquilla->id_taquilla }}
                                (Actual: ${{ number_format($taquilla->monto_actual, 2) }})
                            </flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="id_taquilla" />
                    </div>

                    {{-- Monto inicial --}}
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Monto inicial
                        </label>
                        <flux:input
                            wire:model="monto_inicial"
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                            :disabled="!$id_taquilla" />
                        <flux:error name="monto_inicial" />
                        @if($id_taquilla && $monto_inicial !== null)
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            Monto tomado del saldo actual de la taquilla
                        </p>
                        @endif
                    </div>

                    {{-- Acciones --}}
                    <div class="flex flex-col gap-2 pt-2">
                        <flux:button
                            type="submit"
                            variant="primary"
                            class="w-full"
                            :disabled="!$id_taquilla">
                            Confirmar Apertura
                        </flux:button>
                        <flux:button
                            href="{{ route('dashboard') }}"
                            variant="ghost"
                            class="w-full">
                            Cancelar
                        </flux:button>
                    </div>

                </form>
            </div>

        </div>

        {{-- Nota informativa debajo del card --}}
        <p class="text-center text-xs text-gray-400 dark:text-gray-600 mt-4">
            El monto se carga automáticamente al seleccionar la taquilla
        </p>
    </div>
</div>