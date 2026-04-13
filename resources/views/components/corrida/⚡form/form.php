<?php

use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\Ruta;
use App\Models\User;
use App\Models\Corrida;
use App\Models\Urban;
use Illuminate\Support\Collection;

new class extends Component
{   
    public $id_ruta = '';
    public $id_usuario = '';
    public $hora_llegada;
    public $hora_salida;

    public $id_urban_actual = '';
    public $id_urbans = [];

    public function rules()
    {
        return [
        'id_ruta' => ['required', 'exists:ruta,id_ruta'],
        'id_usuario' => ['required', 'exists:users,id_usuario'],
        'id_urbans' => ['required', 'array', 'min:1'],
        'id_urbans.*' => ['integer', 'exists:urban,id_urban'],
        'fecha' => ['required', 'date'],
        'hora_llegada' => ['required', 'date_format:H:i'],
        'hora_salida' => ['required', 'date_format:H:i'],
    ];
    }

    public function messages()
    {
        return [
            'id_ruta.required' => 'Selecciona una ruta',
            'id_usuario.required' => 'Por favor selecciona un conductor',
            'id_urbans.required' => 'Por favor selecciona al menos un urban',
            'id_urbans.array' => 'Por favor selecciona al menos un urban',
            'fecha.required' => 'Por favor ingresa una fecha',
            'hora_llegada.required' => 'Por favor ingresa una hora de llegada',
            'hora_salida.required' => 'Por favor ingresa una hora de salida',
        ];
    }

    #[Computed]
    public function rutas(){
        return Ruta::orderBy('id_ruta')->get();
    }

    #[Computed]
    public function usuarios(){
        return User::orderBy('id_usuario')->get();
    }

    #[Computed]
    public function urbans(){
        return Urban::orderBy('id_urban')->get();
    }

    #[Computed]
    public function urbansSeleccionadas()
    {
        if (empty($this->id_urbans)) {
            return collect();
        }

        return Urban::query()
            ->whereIn('id_urban', $this->id_urbans)
            ->orderBy('codigo_urban')
            ->get(['id_urban', 'codigo_urban']);
    }

    public function agregarUrban()
    {
        if (!$this->id_urban_actual) return;

        $id = (int) $this->id_urban_actual;

        if (!in_array($id, $this->id_urbans, true)) {
            $this->id_urbans[] = $id;
        }

        $this->id_urban_actual = '';
    }

    public function quitarUrban(int $id)
    {
        $this->id_urbans = array_values(
            array_filter($this->id_urbans, fn ($v) => (int) $v !== $id)
        );
    }

    public function save()
    {
        $this->validate();

        $corrida = Corrida::create([
            'id_ruta' => $this->id_ruta,
            'id_usuario' => $this->id_usuario,
            'fecha' => $this->fecha,
            'hora_llegada' => $this->hora_llegada,
            'hora_salida' => $this->hora_salida,
        ]);

        $corrida->urbans()->sync($this->id_urbans);

        $this->dispatch('corrida-creada');

        $this->reset([
            'id_ruta',
            'id_usuario',
            'id_urbans',
            'id_urban_actual',
            'fecha',
            'hora_llegada',
            'hora_salida',
        ]);
    }
};
