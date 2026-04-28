<?php

use App\Models\Taquilla;
use App\Models\Turno;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public $id_taquilla;
    public $monto_inicial;

    // Escucha cambios en id_taquilla para actualizar el monto automáticamente
    public function updatedIdTaquilla($value)
    {
        $taquilla = Taquilla::find($value);
        if ($taquilla) {
            $this->monto_inicial = $taquilla->monto_actual;
        }
    }

    public function guardar()
    {
        $this->validate([
            'id_taquilla' => 'required|exists:taquilla,id_taquilla',
            'monto_inicial' => 'required|numeric|min:0',
        ]);

        // Evitar duplicados (Doble check)
        $existe = Turno::where('id_usuario', Auth::user()->id_usuario)
            ->whereNull('hora_fin')
            ->exists();

        if ($existe) {
            session()->flash('error', 'Ya tienes un turno activo.');
            return redirect()->route('dashboard');
        }


        Turno::create([
            'id_usuario' => Auth::user()->id_usuario,
            'id_taquilla' => $this->id_taquilla,
            'monto_inicial' => $this->monto_inicial,
            'fecha' => now()->toDateString(),
            'hora_inicio' => now()->toTimeString()
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

<div class="flex items-center justify-center min-h-screen bg-gray-50 dark:bg-zinc-900 px-4">
    <flux:card class="max-w-md w-full shadow-lg">
        <div class="space-y-1 mb-6">
            <flux:heading size="lg">Apertura de Turno</flux:heading>
            <flux:subheading>Selecciona tu taquilla e ingresa el monto base.</flux:subheading>
        </div>

        <form wire:submit="guardar" class="space-y-6">
            <flux:select wire:model.live="id_taquilla" label="Taquilla disponible" placeholder="Seleccione una taquilla...">
                @foreach($taquillas as $taquilla)
                <flux:select.option value="{{ $taquilla->id_taquilla }}">
                    Taquilla #{{ $taquilla->id_taquilla }} (Actual: ${{ number_format($taquilla->monto_actual, 2) }})
                </flux:select.option>
                @endforeach
            </flux:select>

            <flux:input
                wire:model="monto_inicial"
                label="Monto Inicial"
                type="number"
                step="0.01"
                icon="currency-dollar"
                placeholder="0.00" />

            <div class="flex flex-col gap-2">
                <flux:button type="submit" variant="primary" class="w-full">
                    Confirmar Apertura
                </flux:button>

                <flux:button href="/dashboard" variant="ghost" class="w-full">
                    Cancelar
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>