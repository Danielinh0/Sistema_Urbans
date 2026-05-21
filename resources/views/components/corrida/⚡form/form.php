<?php

use App\Models\Corrida;
use App\Models\Ruta;
use App\Models\Urban;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Attributes\On;
use Carbon\Carbon;

new class extends Component
{
    public $id_ruta = '';

    public $fecha = '';
    public $datetime_salida = '';

    public $fecha_llegada = '';
    public $datetime_llegada = '';

    public $id_urban_actual = '';

    public $id_chofer_actual = '';

    public array $asignaciones = [];

    public function rules()
    {
        return [
            'id_ruta' => ['required', 'exists:ruta,id_ruta'],
            'fecha' => ['required','date','date_format:Y-m-d'],
            'datetime_salida' => ['required','date_format:H:i'],
            
            'asignaciones' => ['required', 'array', 'min:1'],
            'asignaciones.*.id_urban' => ['required', 'integer', 'exists:urban,id_urban', 'distinct'],
            'asignaciones.*.id_usuario' => ['required', 'integer', 'exists:users,id_usuario', 'distinct'],
        ];
    }

    public function messages()
    {
        return [
            'id_ruta.required' => 'Selecciona una ruta',
            
            'datetime_salida.required' => 'Por favor ingresa una fecha y hora de salida',
            'asignaciones.required' => 'Agrega al menos una urban con su chofer.',
            'asignaciones.min' => 'Agrega al menos una urban con su chofer.',
            'asignaciones.*.id_urban.required' => 'Selecciona una urban valida.',
            'asignaciones.*.id_usuario.required' => 'Selecciona un chofer valido.',
            'asignaciones.*.id_urban.distinct' => 'No puedes repetir una urban.',
            'asignaciones.*.id_usuario.distinct' => 'No puedes repetir un chofer.',
        ];
    }

    #[On('ruta-eliminada')]
    #[On('ruta-creada')]
    public function refreshRutas()
    {
        $this->dispatch('$refresh');
    }

    #[Computed]
    public function rutas()
    {
        return Ruta::orderBy('id_ruta')->get();
    }

    #[Computed]
    public function conductores()
    {
        return User::role('chofer')->orderBy('id_usuario')->get();
    }

    #[Computed]
    public function conductoresDisponibles()
    {
        $choferesUsados = collect($this->asignaciones)
            ->pluck('id_usuario')
            ->map(fn ($id) => (int) $id)
            ->all();

        return User::role('chofer')
            ->when(
                ! empty($choferesUsados),
                fn ($query) => $query->whereNotIn('id_usuario', $choferesUsados)
            )
            ->orderBy('id_usuario')
            ->get();
    }

    #[Computed]
    public function urbans()
    {
        return Urban::where('estado', 'Libre')->orderBy('id_urban')->get();
    }

    public function agregarAsignacion()
    {
        $this->resetErrorBag('asignaciones');

        if (! $this->id_urban_actual || ! $this->id_chofer_actual) {
            $this->addError('asignaciones', 'Debes seleccionar una urban y un chofer antes de agregar.');

            return;
        }

        $idUrban = (int) $this->id_urban_actual;
        $idUsuario = (int) $this->id_chofer_actual;

        $urbanRepetida = collect($this->asignaciones)->contains(
            fn ($asignacion) => (int) $asignacion['id_urban'] === $idUrban
        );

        $choferRepetido = collect($this->asignaciones)->contains(
            fn ($asignacion) => (int) $asignacion['id_usuario'] === $idUsuario
        );

        if ($urbanRepetida || $choferRepetido) {
            $this->addError(
                'asignaciones',
                $urbanRepetida ? 'Esa urban ya fue agregada.' : 'Ese chofer ya fue agregado.'
            );

            return;
        }

        $this->asignaciones[] = [
            'id_urban' => $idUrban,
            'id_usuario' => $idUsuario,
        ];

        $this->id_urban_actual = '';
        $this->id_chofer_actual = '';
    }

    public function quitarAsignacion(int $index)
    {
        unset($this->asignaciones[$index]);
        $this->asignaciones = array_values($this->asignaciones);
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
        } 

        elseif (is_string($tiempo) && preg_match('/^(\d+):(\d+)(?::(\d+))?$/', $tiempo, $m)) {
            $llegada = $salida->copy()
                ->addHours((int) $m[1])
                ->addMinutes((int) $m[2])
                ->addSeconds(isset($m[3]) ? (int) $m[3] : 0);
        } 
        
        else {
            $llegada = $salida->copy()->addMinutes((int) $tiempo);
        }

        return $llegada;
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            foreach ($this->asignaciones as $asignacion) {
                $salida = Carbon::createFromFormat('Y-m-d H:i', "{$this->fecha} {$this->datetime_salida}");
                $llegada = $this->calcular__llegada($this->fecha, $this->datetime_salida, (int) $this->id_ruta);

                Corrida::create([
                    'id_ruta' => (int) $this->id_ruta,
                    'id_usuario' => (int) $asignacion['id_usuario'],
                    'id_urban' => (int) $asignacion['id_urban'],
                    'datetime_salida' => $salida,
                    'datetime_llegada' => $llegada,
                    'estado' => 'Programada',
                ]);
            }
        });

        $this->dispatch('corrida-creada');

        $this->reset([
            'id_ruta',
            'id_urban_actual',
            'id_chofer_actual',
            'asignaciones',
            'datetime_salida',
            'datetime_llegada',
            'fecha_llegada',
        ]);

        Flux::toast('Your changes have been saved.');
    }

    
};
