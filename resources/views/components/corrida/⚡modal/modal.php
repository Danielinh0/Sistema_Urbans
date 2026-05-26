<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use App\Models\Corrida;
use App\Models\Ruta;
use App\Models\Urban;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

new class extends Component
{
    use AuthorizesRequests;

    public ?Corrida $corrida = null;

    #[Validate('required', message: 'La ruta es requerida.')]
    public $id_ruta;

    #[Validate('required', message: 'La urban es requerida.')]
    public $id_urban;

    #[Validate('required', message: 'El conductor es requerido.')]
    public $id_usuario;

    #[Validate('required|date|date_format:Y-m-d', message: 'La fecha de salida es requerida.')]
    public $fecha;

    #[Validate('required|date_format:H:i', message: 'La hora de salida es requerida.')]
    public $datetime_salida;

    public $fecha_llegada;
    public $datetime_llegada;

    protected $horaSalida;
    protected $horaLlegada;

    #[On('edicion-corrida')]
    public function prepararEdicion($id)
    {
        $this->corrida = Corrida::findOrFail($id);
        $this->authorize('update', $this->corrida);
        
        $this->id_ruta = $this->corrida->id_ruta;
        $this->id_urban = $this->corrida->id_urban;
        $this->id_usuario = $this->corrida->id_usuario;
        
        $this->fecha = $this->corrida->datetime_salida ? $this->corrida->datetime_salida->format('Y-m-d') : null;
        $this->datetime_salida = $this->corrida->datetime_salida ? $this->corrida->datetime_salida->format('H:i') : null;
        $this->fecha_llegada = $this->corrida->datetime_llegada ? $this->corrida->datetime_llegada->format('Y-m-d') : null;
        $this->datetime_llegada = $this->corrida->datetime_llegada ? $this->corrida->datetime_llegada->format('H:i') : null;

        $this->js("Flux.modal('modal-editar-corrida').show()");
    }

    #[On('cambio-estado-corrida')]
    public function prepararCambioEstado($id)
    {
        $this->corrida = Corrida::findOrFail($id);
        $this->authorize('update', $this->corrida);
        $this->js("Flux.modal('modal-estado-corrida').show()");
    }

    #[On('eliminacion-corrida')]
    public function prepararEliminacion($id)
    {
        $this->corrida = Corrida::findOrFail($id);
        $this->authorize('delete', $this->corrida);
        $this->js("Flux.modal('modal-eliminar-corrida').show()");
    }

    #[On('reset-form')]
    public function resetForm()
    {
        $this->reset(['id_ruta', 'id_urban', 'id_usuario', 'fecha', 'datetime_salida', 'fecha_llegada', 'datetime_llegada']);
        $this->resetErrorBag();
    }

    public function updatedIdRuta($value)
    {
        $this->generarLlegada();
    }

    public function updatedFecha($value)
    {
        $this->generarLlegada();
    }

    public function updatedDatetimeSalida($value)
    {
        $this->generarLlegada();
    }

    public function generarLlegada()
    {
        if ($this->fecha && $this->datetime_salida && $this->id_ruta) {
            try {
                $llegada = $this->calcular__llegada($this->fecha, $this->datetime_salida, (int) $this->id_ruta);
                
                $this->fecha_llegada = $llegada->format('Y-m-d');
                $this->datetime_llegada = $llegada->format('H:i');
            } catch (\Exception $e) {
                $this->fecha_llegada = '';
                $this->datetime_llegada = '';
            }
        } else {
            $this->fecha_llegada = '';
            $this->datetime_llegada = '';
        }
    }

    protected function calcular__llegada(string $fecha, string $horaSalida, ?int $idRuta = null)
    {
        $salida = Carbon::createFromFormat('Y-m-d H:i', "{$fecha} {$horaSalida}");

        $tiempo = 0;
        $ruta = Ruta::find($idRuta);
        $tiempo = $ruta->tiempo_estimado ?? 0;

        if (is_numeric($tiempo)) {
            $llegada = $salida->copy()->addMinutes((int) $tiempo);
        } elseif (is_string($tiempo) && preg_match('/^(\d+):(\d+)(?::(\d+))?$/', $tiempo, $m)) {
            $llegada = $salida->copy()
                ->addHours((int) $m[1])
                ->addMinutes((int) $m[2])
                ->addSeconds(isset($m[3]) ? (int) $m[3] : 0);
        } else {
            $llegada = $salida->copy()->addMinutes((int) $tiempo);
        }

        return $llegada;
    }

    private function rangoOcupado(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereBetween('datetime_salida', [$this->horaSalida, $this->horaLlegada])
              ->orWhereBetween('datetime_llegada', [$this->horaSalida, $this->horaLlegada])
              ->orWhere(function ($q2) {
                  $q2->where('datetime_salida', '<', $this->horaSalida)
                     ->where('datetime_llegada', '>', $this->horaLlegada);
              });
        })->when($this->corrida, function($q) {
            // Excluimos la corrida actual de las validaciones de rango para no interferir consigo misma
            $q->where('id_corrida', '!=', $this->corrida->id_corrida);
        });
    }

    private function calcularRango(): bool
    {
        if (!$this->fecha || !$this->datetime_salida || !$this->id_ruta) {
            return false;
        }

        try {
            $this->horaSalida = Carbon::createFromFormat('Y-m-d H:i', "{$this->fecha} {$this->datetime_salida}");
            $this->horaLlegada = $this->calcular__llegada($this->fecha, $this->datetime_salida, (int) $this->id_ruta);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    #[Computed]
    public function urbans_disponibles()
    {
        // Si no hay rango válido, al menos retornar la urban actual si existe
        if (!$this->calcularRango()) {
            if ($this->corrida && $this->corrida->id_urban) {
                return Urban::where('id_urban', $this->corrida->id_urban)->get();
            }
            return collect();
        }

        $urbans = Urban::where('estado', 'Activa')
            ->whereDoesntHave('corrida', fn($q) => $this->rangoOcupado($q))
            ->get();
            
        // Si la urban actual de esta corrida no fue encontrada, la agregamos (esto pasa si el estado de urban no es Activa)
        if ($this->corrida && $this->corrida->id_urban && !$urbans->contains('id_urban', $this->corrida->id_urban)) {
            $urbanActual = Urban::find($this->corrida->id_urban);
            if ($urbanActual) {
                $urbans->push($urbanActual);
            }
        }
        
        return $urbans;
    }

    #[Computed]
    public function choferes_disponibles()
    {
        if (!$this->calcularRango()) {
            if ($this->corrida && $this->corrida->id_usuario) {
                return User::where('id_usuario', $this->corrida->id_usuario)->get();
            }
            return collect();
        }

        $choferes = User::role('chofer')
            ->whereDoesntHave('corridas', fn($q) => $this->rangoOcupado($q))
            ->get();
            
        // Si el conductor actual de esta corrida no fue encontrado
        if ($this->corrida && $this->corrida->id_usuario && !$choferes->contains('id_usuario', $this->corrida->id_usuario)) {
            $conductorActual = User::find($this->corrida->id_usuario);
            if ($conductorActual) {
                $choferes->push($conductorActual);
            }
        }
        
        return $choferes;
    }

    public function update()
    {
        $this->authorize('update', $this->corrida);
        $this->validate();

        $salida = Carbon::createFromFormat('Y-m-d H:i', "{$this->fecha} {$this->datetime_salida}");
        $llegada = $this->calcular__llegada($this->fecha, $this->datetime_salida, (int) $this->id_ruta);

        $this->corrida->update([
            'id_ruta' => $this->id_ruta,
            'id_urban' => $this->id_urban,
            'id_usuario' => $this->id_usuario,
            'datetime_salida' => $salida,
            'datetime_llegada' => $llegada,
        ]);

        $this->js("Flux.modal('modal-editar-corrida').close()");
        $this->dispatch('corrida-actualizada'); 
    }

    public function actualizarEstado($nuevoEstado)
    {
        $this->authorize('update', $this->corrida);

        $this->corrida->update([
            'estado' => $nuevoEstado
        ]);

        $this->js("Flux.modal('modal-estado-corrida').close()");
        $this->dispatch('corrida-actualizada');
    }

    public function delete()
    {
        $this->authorize('delete', $this->corrida);

        $this->corrida->delete();
        $this->js("Flux.modal('modal-eliminar-corrida').close()");
        $this->dispatch('corrida-eliminada');
    }

    #[Computed]
    public function rutas()
    {
        return Ruta::orderBy('nombre')->get();
    }
};
