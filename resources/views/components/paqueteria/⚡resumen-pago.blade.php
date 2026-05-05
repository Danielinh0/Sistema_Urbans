<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Corrida;
use App\Models\Boleto;
use App\Models\BoletoPaquete;
use App\Models\User;
use Illuminate\Support\Facades\DB;

new class extends Component
{
    public $corridaId  = null;
    public $tarifaBase = 0;
    public $peso       = 1;

    public $busquedaCliente = '';
    public $clienteId       = null;

    // Escucha a registro-paquete
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

    // Escucha a form-paquete
    #[On('peso-actualizado')]
    public function actualizarPeso($peso)
    {
        $this->peso = $peso;
    }

    public function seleccionarCliente($id, $nombre)
    {
        $this->clienteId       = $id;
        $this->busquedaCliente = $nombre;
    }

    public function calcularTotal()
    {
        $extra = ($this->peso > 5) ? ($this->peso - 5) * 10 : 0;
        return $this->tarifaBase + $extra;
    }

    public function guardar()
    {
        $this->validate([
            'corridaId' => 'required',
            'clienteId' => 'required',
        ]);

        // Pide los datos del formulario a form-paquete via evento
        $this->dispatch('solicitar-datos-formulario');
    }

    // Recibe los datos de form-paquete y finaliza el guardado
    #[On('datos-formulario')]
    public function finalizarGuardado($guia, $destinatario, $descripcion, $peso, $empaque)
    {
        DB::transaction(function () use ($guia, $destinatario, $descripcion, $peso) {
            $boleto = Boleto::create([
                'id_corrida' => $this->corridaId,
                'id_usuario' => $this->clienteId,
                'precio'     => $this->calcularTotal(),
                'estado'     => 'Pagado',
            ]);

            BoletoPaquete::create([
                'id_boleto'             => $boleto->id_boleto,
                'numero_guia'           => $guia,
                'descripcion_contenido' => $descripcion,
                'peso_kg'               => $peso,
                'nombre_destinatario'   => $destinatario,
            ]);
        });

        session()->flash('status', 'Paquete registrado con éxito.');
        return redirect()->route('paqueteria.index');
    }

    public function with(): array
    {
        $clientes = [];
        if (strlen($this->busquedaCliente) > 2) {
            $clientes = User::where('name', 'like', "%{$this->busquedaCliente}%")
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
        <div class="space-y-6">
            <flux:heading>Resumen de Cobro</flux:heading>

            <!-- Buscador Remitente -->
            <div class="relative">
                <flux:input
                    label="Cliente / Remitente"
                    wire:model.live="busquedaCliente" />

                @if(count($clientes) > 0 && !$clienteId)
                <div class="absolute z-10 w-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded shadow-lg mt-1">
                    @foreach($clientes as $c)
                    <div
                        wire:click="seleccionarCliente({{ $c->id }}, '{{ $c->name }}')"
                        class="p-2 hover:bg-zinc-100 dark:hover:bg-zinc-700 cursor-pointer text-sm">
                        {{ $c->name }}
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Totales -->
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

        <flux:button
            variant="primary"
            class="w-full mt-6"
            wire:click="guardar"
            :disabled="!$corridaId || !$clienteId">
            Registrar y Cobrar
        </flux:button>
    </flux:card>
</div>