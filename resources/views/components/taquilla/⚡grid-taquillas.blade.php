<?php

use Livewire\Component;
use App\Models\Taquilla;
use App\Models\Turno;
use App\Models\User;

new class extends Component
{
    // Estado del modal de apertura
    public $taquillaSeleccionada = null;
    public $cajeroId             = null;
    public $montoInicial         = 0;

    public function abrirModal($idTaquilla)
    {
        $this->taquillaSeleccionada = $idTaquilla;
        $this->cajeroId             = null;
        $this->montoInicial         = 0;
        $this->modal('abrir-taquilla')->show();
    }

    public function confirmarApertura()
    {
        $this->validate([
            'cajeroId'     => 'required|exists:users,id_usuario',
            'montoInicial' => 'required|numeric|min:0',
        ], [
            'cajeroId.required'     => 'Selecciona un cajero.',
            'montoInicial.required' => 'Ingresa el monto inicial.',
        ]);

        // Verificar que el cajero no tenga ya un turno activo
        $turnoExistente = Turno::where('id_usuario', $this->cajeroId)
            ->whereNull('hora_fin')
            ->exists();

        if ($turnoExistente) {
            $this->addError('cajeroId', 'Este cajero ya tiene un turno activo.');
            return;
        }

        Turno::create([
            'id_usuario'    => $this->cajeroId,
            'id_taquilla'   => $this->taquillaSeleccionada,
            'monto_inicial' => $this->montoInicial,
            'fecha'         => now()->toDateString(),
            'hora_inicio'   => now()->toTimeString(),
        ]);

        $this->modal('abrir-taquilla')->close();
        session()->flash('success', "Taquilla #{$this->taquillaSeleccionada} abierta correctamente.");
    }

    public function cerrarTaquilla($idTaquilla)
    {
        $turno = Turno::where('id_taquilla', $idTaquilla)
            ->whereNull('hora_fin')
            ->latest('hora_inicio')
            ->first();

        if ($turno) {
            $turno->update(['hora_fin' => now()->toTimeString()]);
            session()->flash('success', "Taquilla #{$idTaquilla} cerrada.");
        }
    }

    public function with(): array
    {
        $taquillas = Taquilla::all()->map(function ($taquilla) {
            $turnoActivo = Turno::with('user')
                ->where('id_taquilla', $taquilla->id_taquilla)
                ->whereNull('hora_fin')
                ->latest('hora_inicio')
                ->first();

            $taquilla->turno_activo = $turnoActivo;
            $taquilla->esta_abierta = (bool) $turnoActivo;
            $taquilla->taquillero   = $turnoActivo?->user?->name ?? null;

            return $taquilla;
        });

        return [
            'taquillas'      => $taquillas,
            'totalAbiertas'  => $taquillas->where('esta_abierta', true)->count(),
            'totalCerradas'  => $taquillas->where('esta_abierta', false)->count(),
            'cajeros'        => User::role('cajero')->get(), // Spatie
        ];
    }
};
?>

<div class="space-y-4">

    <div class="flex items-center gap-4">
        <flux:heading size="lg">Taquillas</flux:heading>
        <flux:badge color="green">{{ $totalAbiertas }} abiertas</flux:badge>
        <flux:badge color="zinc">{{ $totalCerradas }} cerradas</flux:badge>
    </div>

    @if(session('success'))
    <flux:callout variant="success" icon="circle-plus">
        {{ session('success') }}
    </flux:callout>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($taquillas as $taquilla)
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden flex flex-col">

            <div class="px-4 pt-4 flex items-start justify-between">
                <div>
                    <p class="text-xs text-zinc-400">#{{ $taquilla->id_taquilla }}</p>
                    <p class="font-bold text-zinc-800 dark:text-white text-base leading-tight">
                        {{ $taquilla->nombre ?? 'Taquilla ' . $taquilla->id_taquilla }}
                    </p>
                </div>
                @if($taquilla->esta_abierta)
                <flux:badge color="green" size="sm">ABIERTA</flux:badge>
                @else
                <flux:badge color="zinc" size="sm">CERRADA</flux:badge>
                @endif
            </div>

            <div class="flex justify-center py-5">
                <div class="p-4 rounded-full
                        {{ $taquilla->esta_abierta
                            ? 'bg-blue-50 dark:bg-blue-900/20'
                            : 'bg-zinc-100 dark:bg-zinc-800' }}">
                    <flux:icon name="tickets"
                        class="size-12 {{ $taquilla->esta_abierta
                                ? 'text-blue-600 dark:text-blue-400'
                                : 'text-zinc-400' }}" />
                </div>
            </div>

            <div class="px-4 pb-4 space-y-2 text-sm flex-1">
                <div class="flex justify-between">
                    <span class="text-zinc-400">Taquillero:</span>
                    <span class="font-medium text-zinc-700 dark:text-zinc-200 text-right">
                        {{ $taquilla->taquillero ?? '—' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-zinc-400">Monto:</span>
                    <span class="font-medium text-zinc-700 dark:text-zinc-200">
                        ${{ number_format($taquilla->monto_actual, 2) }}
                    </span>
                </div>
                @if(!$taquilla->taquillero)
                <p class="text-xs text-zinc-400 italic text-center pt-1">
                    Sin cajero asignado
                </p>
                @endif
            </div>

            <div class="px-4 pb-4">
                @if($taquilla->esta_abierta)
                {{-- Rojo más oscuro y sobrio --}}
                <flux:button
                    variant="primary"
                    class="w-full "
                    wire:click="cerrarTaquilla({{ $taquilla->id_taquilla }})"
                    wire:confirm="¿Cerrar la taquilla {{ $taquilla->nombre ?? '#' . $taquilla->id_taquilla }}?">
                    Cerrar Taquilla
                </flux:button>
                @else
                {{-- Azul primario --}}
                <flux:button
                    variant="primary"
                    class="w-full !bg-blue-800 hover:!bg-blue-900 !border-blue-800 !text-white"
                    wire:click="abrirModal({{ $taquilla->id_taquilla }})">
                    Abrir Taquilla
                </flux:button>
                @endif
            </div>

        </div>
        @endforeach
    </div>

    {{-- ✅ Modal de apertura --}}
    <flux:modal name="abrir-taquilla" class="max-w-md">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Abrir Taquilla #{{ $taquillaSeleccionada }}</flux:heading>
                <flux:subheading>Asigna un cajero y el monto inicial.</flux:subheading>
            </div>

            <flux:select
                wire:model="cajeroId"
                label="Cajero"
                placeholder="Selecciona un cajero...">
                @foreach($cajeros as $cajero)
                <flux:select.option value="{{ $cajero->id_usuario }}">
                    {{ $cajero->name }}
                </flux:select.option>
                @endforeach
            </flux:select>
            <flux:error name="cajeroId" />

            <flux:input
                wire:model="montoInicial"
                label="Monto Inicial"
                type="number"
                step="0.01"
                icon="currency-dollar"
                placeholder="0.00" />
            <flux:error name="montoInicial" />

            <div class="flex gap-2">
                <flux:button
                    variant="primary"
                    class="flex-1"
                    wire:click="confirmarApertura">
                    Confirmar Apertura
                </flux:button>
                <flux:button
                    variant="ghost"
                    x-on:click="$flux.modal('abrir-taquilla').close()">
                    Cancelar
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>