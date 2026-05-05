<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Corrida;
use App\Models\Boleto;
use App\Models\BoletoPaquete;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\DetalleVenta;
use Flux\Flux;
use Illuminate\Support\Facades\DB;

new class extends Component
{
    public $corridaId  = null;
    public $tarifaBase = 0;
    public $peso       = 1;

    // Propiedades para el Toast
    public $flashMsg = '';
    public $flashType = 'success';

    // Búsqueda de cliente existente
    public $busquedaCliente  = '';
    public $clienteId        = null;

    // Campos del remitente
    public $nombre           = '';
    public $apellidoPaterno  = '';
    public $apellidoMaterno  = '';

    #[On('corrida-seleccionada')]
    public function actualizarCorrida($id)
    {
        $this->corridaId  = $id;
        $corrida          = Corrida::with('ruta')->find($id);
        $this->tarifaBase = $corrida?->ruta?->tarifa_clientes ?? 0;
    }

    #[On('corrida-deseleccionada')]
    public function quitarCorrida()
    {
        $this->corridaId  = null;
        $this->tarifaBase = 0;
    }

    #[On('peso-actualizado')]
    public function actualizarPeso($peso)
    {
        $this->peso = $peso;
    }

    public function seleccionarCliente($id, $nombre, $apellidoPaterno, $apellidoMaterno)
    {
        $this->clienteId       = $id;
        $this->nombre          = $nombre;
        $this->apellidoPaterno = $apellidoPaterno;
        $this->apellidoMaterno = $apellidoMaterno;
        $this->busquedaCliente = $nombre . ' ' . $apellidoPaterno;
    }

    public function limpiarCliente()
    {
        $this->clienteId       = null;
        $this->busquedaCliente = '';
        $this->nombre          = '';
        $this->apellidoPaterno = '';
        $this->apellidoMaterno = '';
    }

    public function calcularTotal()
    {
        $extra = ($this->peso > 5) ? ($this->peso - 5) * 10 : 0;
        return $this->tarifaBase + $extra;
    }

    public function guardar()
    {
        $this->validate([
            'corridaId'       => 'required',
            'nombre'          => 'required|string|max:100',
            'apellidoPaterno' => 'required|string|max:100',
        ], [
            'corridaId.required'       => 'Error: No se ha detectado una corrida seleccionada.',
            'nombre.required'          => 'El nombre es obligatorio.',
            'apellidoPaterno.required' => 'El apellido paterno es obligatorio.',
        ]);

        $this->dispatch('solicitar-datos-formulario');
    }

    #[On('datos-formulario')]
    public function finalizarGuardado($guia, $destinatario, $descripcion, $peso, $empaque)
    {
        try {
            DB::transaction(function () use ($guia, $destinatario, $descripcion, $peso) {

                // 1. Crear o recuperar Cliente
                if (!$this->clienteId) {
                    $cliente = Cliente::create([
                        'nombre'           => $this->nombre,
                        'apellido_paterno' => $this->apellidoPaterno,
                        'apellido_materno' => $this->apellidoMaterno,
                    ]);
                    $this->clienteId = $cliente->id_cliente;
                }

                $total = $this->calcularTotal();

                // 2. Crear Venta
                $venta = Venta::create([
                    'id_cliente' => $this->clienteId,
                    'total'      => $total,
                    'subtotal'   => $total,
                    'descuento'  => 0,
                    'fecha'      => now()->toDateString(),
                ]);

                // 3. Crear Detalle
                $detalle = DetalleVenta::create([
                    'id_venta' => $venta->id_venta,
                ]);

                // 4. Crear Boleto
                // NOTA: Asegúrate que los nombres de las columnas coincidan con tu BD
                $boleto = Boleto::create([
                    'id_corrida'       => $this->corridaId,
                    'id_detalle_venta' => $detalle->id_detalle_venta,
                    'id_cliente'       => $this->clienteId,
                    'folio'            => $guia,
                    'estado'           => 'Pagado',
                    'tipo_de_pago'     => 'Efectivo',
                    'descuento'        => 0,
                ]);

                // 5. Crear BoletoPaquete
                BoletoPaquete::create([
                    'id_boleto'    => $boleto->id_boleto,
                    'guia'         => $guia,
                    'descripcion'  => $descripcion,
                    'peso'         => $peso,
                    'destinatario' => $destinatario,
                ]);
            });

            // 2. Avisamos al componente hermano (form-paquete) que se limpie
            $this->dispatch('limpiar-formulario-paquete');

            // 3. Activamos el mensaje Pop-up (sin sesión, porque no hay redirección)
            $this->flashMsg = 'Venta registrada con éxito.';
            $this->flashType = 'success';
        } catch (\Exception $e) {
            $this->flashMsg = "Error: " . $e->getMessage();
            $this->flashType = 'error';
        }
    }

    public function with(): array
    {
        $clientes = [];
        if (strlen($this->busquedaCliente) > 2 && !$this->clienteId) {
            $clientes = Cliente::where('nombre', 'like', "%{$this->busquedaCliente}%")
                ->orWhere('apellido_paterno', 'like', "%{$this->busquedaCliente}%")
                ->limit(5)
                ->get();
        }

        return [
            'clientes' => $clientes,
            'total'    => $this->calcularTotal(),
        ];
    }
};
?>

<div>
    <flux:card class="flex flex-col justify-between h-full">
        <div class="space-y-4">
            <flux:heading>Resumen de Cobro</flux:heading>

            {{-- Buscador cliente existente --}}
            <div class="relative">
                <flux:input
                    label="Buscar cliente existente"
                    wire:model.live="busquedaCliente"
                    placeholder="Escribe nombre o apellido..."
                    :disabled="(bool) $clienteId" />

                @if(count($clientes) > 0)
                <div class="absolute z-10 w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-lg mt-1">
                    @foreach($clientes as $c)
                    <div
                        wire:click="seleccionarCliente(
                                    {{ $c->id_cliente }},
                                    '{{ $c->nombre }}',
                                    '{{ $c->apellido_paterno }}',
                                    '{{ $c->apellido_materno }}'
                                )"
                        class="p-2 hover:bg-zinc-100 dark:hover:bg-zinc-700 cursor-pointer text-sm">
                        {{ $c->nombre }} {{ $c->apellido_paterno }} {{ $c->apellido_materno }}
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Indicador cliente seleccionado --}}
            @if($clienteId)
            <div class="flex items-center justify-between px-3 py-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 text-sm">
                <span class="text-blue-700 dark:text-blue-300 font-medium">
                    Cliente: {{ $nombre }} {{ $apellidoPaterno }}
                </span>
                <button wire:click="limpiarCliente"
                    class="text-blue-400 hover:text-blue-600 dark:hover:text-blue-200">
                    <flux:icon name="x" class="size-4" />
                </button>
            </div>
            @endif

            {{-- Campos del remitente --}}
            <div class="space-y-3">
                <flux:input
                    label="Nombre(s)"
                    wire:model="nombre"
                    placeholder="Ej: Juan Carlos"
                    :readonly="(bool) $clienteId" />
                <flux:error name="nombre" />

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <flux:input
                            label="Apellido Paterno"
                            wire:model="apellidoPaterno"
                            placeholder="Ej: García"
                            :readonly="(bool) $clienteId" />
                        <flux:error name="apellidoPaterno" />
                    </div>
                    <div>
                        <flux:input
                            label="Apellido Materno"
                            wire:model="apellidoMaterno"
                            placeholder="Ej: López"
                            :readonly="(bool) $clienteId" />
                    </div>
                </div>
            </div>

            {{-- Totales --}}
            <div class="p-4 bg-zinc-50 dark:bg-zinc-900 rounded-lg border space-y-2">
                <div class="flex justify-between text-sm text-zinc-500">
                    <span>Tarifa base:</span>
                    <span>${{ number_format($tarifaBase, 2) }}</span>
                </div>
                <div class="flex justify-between font-bold text-lg border-t pt-2">
                    <span>Total:</span>
                    <span class="text-emerald-600">${{ number_format($total, 2) }}</span>
                </div>
            </div>
        </div>

        @if ($errors->any())
        <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
            @foreach ($errors->all() as $error)
            <p class="text-xs text-red-600">{{ $error }}</p>
            @endforeach
        </div>
        @endif

        <flux:button
            variant="primary"
            class="w-full mt-6"
            wire:click="guardar">
            Registrar y Cobrar
        </flux:button>
    </flux:card>

    {{--Notificación Toast--}}
    @php
    $message = session('flashMsg') ?? $flashMsg;
    $type = session('flashType') ?? $flashType;
    @endphp

    @if($message)
    <div
        x-data="{ visible: true }"
        x-init="setTimeout(() => { visible = false; $wire.set('flashMsg', '') }, 4000)"
        x-show="visible"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        wire:key="toast-{{ $message }}"
        class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-5 py-4 rounded-2xl shadow-2xl text-sm font-medium max-w-sm
        {{ $type === 'success'
            ? 'bg-emerald-50 dark:bg-emerald-900/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700'
            : 'bg-red-50 dark:bg-red-900/80 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-700' }}">

        @if($type === 'success')
        <flux:icon.circle-plus class="size-5 shrink-0 text-emerald-500" />
        @else
        <flux:icon.circle-minus class="size-5 shrink-0 text-red-500" />
        @endif

        <span class="flex-1">{{ $message }}</span>

        <button
            x-on:click="visible = false; $wire.set('flashMsg', '')"
            class="ml-1 opacity-50 hover:opacity-100 transition-opacity">
            <flux:icon.x class="size-4" />
        </button>
    </div>
    @endif
</div>