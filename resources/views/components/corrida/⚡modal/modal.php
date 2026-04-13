<?php

use App\Models\Corrida;
use App\Models\Manejada;
use App\Models\Ruta;
use App\Models\Urban;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public ?Corrida $corrida = null;

    public $id_ruta = '';
    public $id_usuario = '';
    public $hora_llegada;
    public $hora_salida;
    public $fecha;
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
    public function rutas()
    {
        return Ruta::query()->orderBy('id_ruta')->get(['id_ruta', 'nombre']);
    }

    #[Computed]
    public function usuarios()
    {
        return User::query()->orderBy('name')->get(['id_usuario', 'name']);
    }

    #[Computed]
    public function urbans()
    {
        return Urban::query()->orderBy('codigo_urban')->get(['id_urban', 'codigo_urban']);
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

    #[On('edicion-corrida')]
    public function prepararEdicion($id)
    {
        $this->corrida = Corrida::with(['manejadas'])->findOrFail($id);

        $this->id_ruta = (string) $this->corrida->id_ruta;
        $this->fecha = $this->corrida->fecha;
        $this->hora_salida = $this->corrida->hora_salida;
        $this->hora_llegada = $this->corrida->hora_llegada;

        $this->id_urbans = $this->corrida->manejadas
            ->pluck('id_urban')
            ->unique()
            ->values()
            ->map(fn ($idUrban) => (int) $idUrban)
            ->all();

        $this->id_usuario = (string) ($this->corrida->manejadas->first()->id_usuario ?? '');
        $this->id_urban_actual = '';

        $this->resetValidation();
        $this->js("Flux.modal('modal-editar-corrida').show()");
    }

    #[On('eliminacion-corrida')]
    public function prepararEliminacion($id)
    {
        $this->corrida = Corrida::with('ruta')->findOrFail($id);
        $this->js("Flux.modal('modal-eliminar-corrida').show()");
    }

    public function agregarUrban()
    {
        if (!$this->id_urban_actual) {
            return;
        }

        $id = (int) $this->id_urban_actual;

        if (!in_array($id, $this->id_urbans, true)) {
            $this->id_urbans[] = $id;
        }

        $this->id_urban_actual = '';
    }

    public function quitarUrban(int $id)
    {
        $this->id_urbans = array_values(
            array_filter($this->id_urbans, fn ($valor) => (int) $valor !== $id)
        );
    }

    protected function buildManejadaIds(): array
    {
        $manejadaIds = [];

        foreach ($this->id_urbans as $idUrban) {
            $manejada = Manejada::firstOrCreate([
                'fecha' => $this->fecha,
                'id_usuario' => (int) $this->id_usuario,
                'id_urban' => (int) $idUrban,
            ]);

            $manejadaIds[] = $manejada->id_manejada;
        }

        return $manejadaIds;
    }

    public function update()
    {
        if (!$this->corrida) {
            return;
        }

        $this->validate();

        $this->corrida->update([
            'id_ruta' => $this->id_ruta,
            'fecha' => $this->fecha,
            'hora_salida' => $this->hora_salida,
            'hora_llegada' => $this->hora_llegada,
        ]);

        $this->corrida->manejadas()->sync($this->buildManejadaIds());

        Manejada::query()->doesntHave('corridas')->delete();

        $this->js("Flux.modal('modal-editar-corrida').close()");
        $this->dispatch('corrida-actualizada');
    }

    public function delete()
    {
        if (!$this->corrida) {
            return;
        }

        $this->corrida->delete();

        Manejada::query()->doesntHave('corridas')->delete();

        $this->js("Flux.modal('modal-eliminar-corrida').close()");
        $this->dispatch('corrida-eliminada');
    }
};