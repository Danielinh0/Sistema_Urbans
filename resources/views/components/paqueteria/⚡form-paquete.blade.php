<?php

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

new class extends Component
{
    public $corridaInfo = null;
    public $guia;
    public $destinatario = '';
    public $descripcion = '';
    public $peso = 1;
    public $empaque = 'caja';

    public function mount($corridaInfo = null)
    {
        $this->corridaInfo = $corridaInfo;
        $this->generarGuia();
    }

    public function generarGuia()
    {
        $this->guia = 'PKG-' . strtoupper(Str::random(6));
    }

    // Cada vez que cambia el peso, avisa a resumen-pago
    public function updatedPeso($value)
    {
        $this->dispatch('peso-actualizado', peso: $value);
    }

    public function with(): array
    {
        return [];
    }

    #[On('solicitar-datos-formulario')]
    public function enviarDatos()
    {
        $this->dispatch(
            'datos-formulario',
            guia: $this->guia,
            destinatario: $this->destinatario,
            descripcion: $this->descripcion,
            peso: $this->peso,
            empaque: $this->empaque,
        );
    }

    // En el componente paqueteria.form-paquete

    #[On('limpiar-formulario-paquete')]
    public function limpiar()
    {
        // Reseteamos los campos del paquete
        $this->reset(['destinatario', 'descripcion', 'peso', 'empaque']);

        // Generamos una nueva guía para la siguiente venta
        $this->generarGuia();
    }
};
?>

<div>
    <flux:card class="space-y-4">
        <div class="space-y-0.5">
            <flux:heading size="xl" class="text-zinc-900 dark:text-white font-semibold">
                Detalles del Envío
            </flux:heading>
            @if($corridaInfo)
            <flux:subheading class="text-zinc-400 dark:text-zinc-500 text-sm">
                Corrida: {{ $corridaInfo['ruta'] }} · Salida: {{ $corridaInfo['salida'] }}
            </flux:subheading>
            @endif
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div class="flex gap-2 items-end">
                <flux:input label="No. Guía" wire:model="guia" readonly class="flex-1" />
                <flux:button icon="refresh-cw" wire:click="generarGuia" variant="ghost" />
            </div>

            <flux:input
                label="Destinatario"
                wire:model="destinatario"
                placeholder="Nombre completo" />

            <div class="col-span-2">
                <flux:textarea
                    label="Descripción"
                    wire:model="descripcion"
                    placeholder="¿Qué contiene el paquete?" />
            </div>

            <flux:input
                label="Peso (kg)"
                type="number"
                wire:model.live="peso" />

            <flux:select label="Empaque" wire:model="empaque">
                <flux:select.option value="caja">Caja</flux:select.option>
                <flux:select.option value="sobre">Sobre</flux:select.option>
            </flux:select>
        </div>
    </flux:card>
</div>