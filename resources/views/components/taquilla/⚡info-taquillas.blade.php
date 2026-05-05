<?php

use Livewire\Component;

new class extends Component
{
    public $horarioInicio = '06:00';
    public $horarioFin    = '22:00';

    public function with(): array
    {
        $ahora         = now();
        $dentroHorario = $ahora->between(
            today()->setTimeFromTimeString($this->horarioInicio),
            today()->setTimeFromTimeString($this->horarioFin)
        );

        return [
            'dentroHorario' => $dentroHorario,
            'horaActual'    => $ahora->format('H:i'),
            'fecha'         => $ahora->translatedFormat('l, d \d\e F \d\e Y'),
        ];
    }
};
?>

<div>
    <flux:card class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">

        <div class="flex items-center gap-4">
            <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                <flux:icon name="building-2" class="size-7 text-blue-600 dark:text-blue-400" />
            </div>
            <div>
                <flux:heading size="xl" class="font-bold">
                    Administración de Taquillas
                </flux:heading>
                <flux:subheading class="text-zinc-400 text-sm capitalize">
                    {{ $fecha }}
                </flux:subheading>
            </div>
        </div>

        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                <flux:icon name="alarm-clock" class="size-4" />
                <span>{{ $horarioInicio }} – {{ $horarioFin }}</span>
            </div>

            <div class="flex items-center gap-2">
                <flux:icon name="activity" class="size-4 text-zinc-400" />
                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">
                    {{ $horaActual }}
                </span>
                @if($dentroHorario)
                <flux:badge color="green" size="sm">En horario</flux:badge>
                @else
                <flux:badge color="red" size="sm">Fuera de horario</flux:badge>
                @endif
            </div>
        </div>

    </flux:card>
</div>