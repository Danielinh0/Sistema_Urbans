<?php

use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Ruta;
use App\Models\User;
use App\Models\Corrida;
use App\Models\Urban;
use Illuminate\Support\Facades\DB;

new class extends Component
{
    public $id_ruta = '';
    public $hora_llegada;
    public $hora_salida;
    public $fecha;
    public $id_urban_actual = '';
    public $id_chofer_actual = '';
    public array $asignaciones = [];

    public function rules()
    {
        return [
            'id_ruta' => ['required', 'exists:ruta,id_ruta'],
            'fecha' => ['required', 'date'],
            'hora_llegada' => ['required', 'date_format:H:i'],
            'hora_salida' => ['required', 'date_format:H:i'],

            'asignaciones' => ['required', 'array', 'min:1'],
            'asignaciones.*.id_urban' => ['required', 'integer', 'exists:urban,id_urban', 'distinct'],
            'asignaciones.*.id_usuario' => ['required', 'integer', 'exists:users,id_usuario', 'distinct'],
        ];
    }

    public function messages()
    {
        return [
            'id_ruta.required' => 'Selecciona una ruta',
            'fecha.required' => 'Por favor ingresa una fecha',
            'hora_llegada.required' => 'Por favor ingresa una hora de llegada',
            'hora_salida.required' => 'Por favor ingresa una hora de salida',
            'asignaciones.required' => 'Agrega al menos una urban con su chofer.',
            'asignaciones.min' => 'Agrega al menos una urban con su chofer.',
            'asignaciones.*.id_urban.required' => 'Selecciona una urban valida.',
            'asignaciones.*.id_usuario.required' => 'Selecciona un chofer valido.',
            'asignaciones.*.id_urban.distinct' => 'No puedes repetir una urban.',
            'asignaciones.*.id_usuario.distinct' => 'No puedes repetir un chofer.',
        ];
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
            ->map(fn($id) => (int) $id)
            ->all();

        return User::role('chofer')
            ->when(
                !empty($choferesUsados),
                fn($query) => $query->whereNotIn('id_usuario', $choferesUsados)
            )
            ->orderBy('id_usuario')
            ->get();
    }

    #[Computed]
    public function urbans()
    {
        return Urban::orderBy('id_urban')->get();
    }

    #[Computed]
    public function urbansDisponibles()
    {
        $urbansUsadas = collect($this->asignaciones)
            ->pluck('id_urban')
            ->map(fn($id) => (int) $id)
            ->all();

        return Urban::query()
            ->when(
                !empty($urbansUsadas),
                fn($query) => $query->whereNotIn('id_urban', $urbansUsadas)
            )
            ->orderBy('id_urban')
            ->get();
    }

    public function agregarAsignacion()
    {
        $this->resetErrorBag('asignaciones');

        if (!$this->id_urban_actual || !$this->id_chofer_actual) {
            $this->addError('asignaciones', 'Debes seleccionar una urban y un chofer antes de agregar.');
            return;
        }

        $idUrban = (int) $this->id_urban_actual;
        $idUsuario = (int) $this->id_chofer_actual;

        $urbanRepetida = collect($this->asignaciones)->contains(
            fn($asignacion) => (int) $asignacion['id_urban'] === $idUrban
        );

        $choferRepetido = collect($this->asignaciones)->contains(
            fn($asignacion) => (int) $asignacion['id_usuario'] === $idUsuario
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

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            foreach ($this->asignaciones as $asignacion) {
                Corrida::create([
                    'id_ruta' => (int) $this->id_ruta,
                    'id_usuario' => (int) $asignacion['id_usuario'],
                    'id_urban' => (int) $asignacion['id_urban'],
                    'fecha' => $this->fecha,
                    'hora_llegada' => $this->hora_llegada,
                    'hora_salida' => $this->hora_salida,
                ]);
            }
        });


        $this->dispatch('corrida-creada');

        $this->reset([
            'id_ruta',
            'id_urban_actual',
            'id_chofer_actual',
            'asignaciones',
            'fecha',
            'hora_llegada',
            'hora_salida',
        ]);
    }
};
