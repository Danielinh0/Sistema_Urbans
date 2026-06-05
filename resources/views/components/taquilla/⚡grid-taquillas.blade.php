<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Taquilla;
use App\Models\Turno;
use App\Models\User;

new class extends Component
{
    // Estado del modal de apertura
    public $taquillaSeleccionada = null;
    public $cajeroId             = null;
    public $montoInicial         = 0;

    // Propiedades para Eliminar
    public $taquillaAEliminar = null;
public $errorEliminacion  = null;

// Propiedades para retiro
public $taquillaRetiro  = null;
public $montoRetiro     = 1;
public $maxMontoRetiro  = 0;
public $retiroMensaje = null;

public function prepararRetiro($idTaquilla)
{
    $taquilla = Taquilla::findOrFail($idTaquilla);
    $this->retiroExcedido = false;
$this->retiroMensaje  = null;

    $this->taquillaRetiro  = $taquilla;
    $this->maxMontoRetiro  = (float) $taquilla->monto_actual;
    $this->montoRetiro     = 1;
    $this->retiroExcedido  = false;
    $this->modal('retirar-monto')->show();
}

public function confirmarRetiro()
{
    $this->validate([
        'montoRetiro' => [
            'required',
            'numeric',
            'min:1',
            "max:{$this->maxMontoRetiro}",
        ],
    ], [
        'montoRetiro.required' => 'Ingresa un monto.',
        'montoRetiro.numeric'  => 'El monto debe ser numérico.',
        'montoRetiro.min'      => 'El monto mínimo a retirar es $1.',
        'montoRetiro.max'      => "No puedes retirar más de $$this->maxMontoRetiro.",
    ]);

    $taquilla = Taquilla::findOrFail($this->taquillaRetiro->id_taquilla);
    $taquilla->decrement('monto_actual', $this->montoRetiro);

    $this->taquillaRetiro = null;
    $this->modal('retirar-monto')->close();
    session()->flash('success', "Retiro de $$this->montoRetiro realizado correctamente.");
}

public $retiroExcedido = false;

public function updatedMontoRetiro($value): void
{
    $val = (float) $value;
    $max = (float) $this->maxMontoRetiro;

    if ($value === '' || $value === null) {
        $this->retiroExcedido = true;
        $this->retiroMensaje  = 'Ingresa un monto a retirar.';
    } elseif ($val <= 0) {
        $this->retiroExcedido = true;
        $this->retiroMensaje  = 'El monto debe ser mayor a $0.';
    } elseif ($val > $max) {
        $this->retiroExcedido = true;
        $this->retiroMensaje  = 'El monto excede el saldo disponible de $' . number_format($max, 2) . '.';
    } else {
        $this->retiroExcedido = false;
        $this->retiroMensaje  = null;
    }
}

public function prepararEliminacion($idTaquilla)
{
    $taquilla = Taquilla::findOrFail($idTaquilla);
    $this->errorEliminacion = null;

    // Validar si está abierta
    $estaAbierta = Turno::where('id_taquilla', $idTaquilla)
        ->whereNull('hora_fin')
        ->exists();

    if ($estaAbierta) {
        $this->errorEliminacion = 'No se puede eliminar una taquilla que está abierta.';
        $this->taquillaAEliminar = $taquilla;
        $this->modal('eliminar-taquilla')->show();
        return;
    }

    // Validar si tiene dinero
    if ($taquilla->monto_actual > 0) {
        $this->errorEliminacion = "La taquilla tiene $" . number_format($taquilla->monto_actual, 2) . " de saldo. Retira el dinero antes de eliminarla.";
        $this->taquillaAEliminar = $taquilla;
        $this->modal('eliminar-taquilla')->show();
        return;
    }

    $this->taquillaAEliminar = $taquilla;
    $this->modal('eliminar-taquilla')->show();
}

public function confirmarEliminacion()
{
    if (!$this->taquillaAEliminar || $this->errorEliminacion) {
        return;
    }

    Taquilla::findOrFail($this->taquillaAEliminar->id_taquilla)->delete();

    $this->taquillaAEliminar = null;
    $this->modal('eliminar-taquilla')->close();
    session()->flash('success', 'Taquilla eliminada correctamente.');
}



    public function abrirModal($idTaquilla)
{
    $taquilla = Taquilla::findOrFail($idTaquilla);

    $this->taquillaSeleccionada = $idTaquilla;
    $this->cajeroId             = null;
    $this->montoInicial         = $taquilla->monto_actual; // ← monto actual de la taquilla
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

    #[On('taquilla-creada')]
public function refrescar(): void
{
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

            <div class="px-4 pb-4 space-y-2">
    @if($taquilla->esta_abierta)
    <flux:button
        variant="primary"
        class="w-full"
        wire:click="cerrarTaquilla({{ $taquilla->id_taquilla }})"
        wire:confirm="¿Cerrar la taquilla {{ $taquilla->nombre ?? '#' . $taquilla->id_taquilla }}?">
        Cerrar Taquilla
    </flux:button>
    @else
    <flux:button
        variant="primary"
        class="w-full !bg-blue-800 hover:!bg-blue-900 !border-blue-800 !text-white"
        wire:click="abrirModal({{ $taquilla->id_taquilla }})">
        Abrir Taquilla
    </flux:button>
    @endif

    {{-- Botón retirar monto --}}
    <flux:button
    variant="ghost"
    class="w-full !border !border-green-400 !text-green-600 hover:!bg-green-50
           dark:!border-green-700 dark:!text-green-400 dark:hover:!bg-green-950/30"
    wire:click="prepararRetiro({{ $taquilla->id_taquilla }})"
    :disabled="$taquilla->monto_actual <= 0">
    Retirar Monto
</flux:button>


    {{-- Botón eliminar siempre visible --}}
    <button
        wire:click="prepararEliminacion({{ $taquilla->id_taquilla }})"
        class="w-full px-4 py-2 rounded-lg text-sm font-medium
               bg-red-100 hover:bg-red-200
               text-red-700
               border border-red-400 hover:border-red-500
               dark:bg-red-950/40 dark:hover:bg-red-950/60
               dark:text-red-400
               dark:border-red-800 dark:hover:border-red-700
               transition-colors duration-150 cursor-pointer">
        Eliminar Taquilla
    </button>
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
    label="Cajero">
    <flux:select.option value="">Seleccionar cajero</flux:select.option>  {{-- ← quitar disabled selected --}}
    @foreach($cajeros as $cajero)
    <flux:select.option value="{{ $cajero->id_usuario }}">
        {{ $cajero->name }}
    </flux:select.option>
    @endforeach
</flux:select>
           

            <flux:input
                wire:model="montoInicial"
                label="Monto Inicial"
                type="number"
                step="0.01"
                icon="currency-dollar"
                />
            <flux:error name="montoInicial" />

            <div class="flex gap-2">
                <flux:button
                    variant="primary"
                    class="flex-1 bg-blue-800 hover:bg-blue-900 border-blue-800 text-white"
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


    {{-- Modal de eliminación --}}
<flux:modal name="eliminar-taquilla" class="max-w-md">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Eliminar Taquilla</flux:heading>
            <flux:subheading>
                Taquilla #{{ $taquillaAEliminar?->id_taquilla }}
            </flux:subheading>
        </div>

        @if($errorEliminacion)
            {{-- Estado: no se puede eliminar --}}
            <flux:callout variant="danger" icon="exclamation-triangle">
                {{ $errorEliminacion }}
            </flux:callout>

            <div class="flex justify-end">
                <flux:button
                    variant="ghost"
                    x-on:click="$flux.modal('eliminar-taquilla').close()">
                    Entendido
                </flux:button>
            </div>
        @else
            {{-- Estado: confirmación --}}
            <flux:text>
                ¿Estás seguro de que deseas eliminar la taquilla
                <b>#{{ $taquillaAEliminar?->id_taquilla }}</b>?
                Esta acción no se puede deshacer.
            </flux:text>

            <div class="flex gap-2 justify-end">
                <flux:button
                    variant="ghost"
                    x-on:click="$flux.modal('eliminar-taquilla').close()">
                    Cancelar
                </flux:button>
                <flux:button
                    variant="danger"
                    wire:click="confirmarEliminacion">
                    Sí, eliminar
                </flux:button>
            </div>
        @endif
    </div>
</flux:modal>

{{-- Modal de retiro --}}
<flux:modal name="retirar-monto" class="max-w-sm">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Retirar Monto</flux:heading>
            <flux:subheading>
                Taquilla #{{ $taquillaRetiro?->id_taquilla }}
                &mdash; Disponible:
                <span class="font-semibold text-zinc-700 dark:text-zinc-200">
                    ${{ number_format($taquillaRetiro?->monto_actual ?? 0, 2) }}
                </span>
            </flux:subheading>
        </div>

        <flux:input
            wire:model.live="montoRetiro"
            label="Monto a retirar"
            type="number"
            min="1"
            max="{{ $maxMontoRetiro }}"
            step="1"
            icon="currency-dollar"
            onkeydown="return /[0-9]/.test(event.key) || ['Backspace','Delete','ArrowLeft','ArrowRight','Tab'].includes(event.key)"  />
       

        {{-- Barra de progreso visual --}}
        @if($maxMontoRetiro > 0)
        <div class="space-y-1">
            <div class="flex justify-between text-xs text-zinc-400">
                <span>$0</span>
                <span>${{ number_format($maxMontoRetiro, 2) }}</span>
            </div>
            <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
                <div
    class="bg-green-400 h-2 rounded-full transition-all duration-200"
    style="width: {{ min(100, ((float)$montoRetiro / (float)$maxMontoRetiro) * 100) }}%">
</div>
            </div>
        </div>
        @endif

        {{-- Advertencia reactiva --}}
@if($retiroExcedido && $retiroMensaje)
<flux:callout variant="danger" icon="exclamation-triangle">
    {{ $retiroMensaje }}
</flux:callout>
@endif
<flux:error name="montoRetiro" />

    <div class="flex gap-2 justify-end">
    <flux:button
        variant="ghost"
        x-on:click="$flux.modal('retirar-monto').close()">
        Cancelar
    </flux:button>
    <flux:button
        variant="primary"
        class="!bg-blue-800 hover:!bg-blue-900 !border-blue-800 !text-white
               disabled:!opacity-50 disabled:!cursor-not-allowed"
        wire:click="confirmarRetiro"
        :disabled="$retiroExcedido || $montoRetiro <= 0">
        Confirmar Retiro
    </flux:button>
</div>        


    </div>
</flux:modal>
</div>