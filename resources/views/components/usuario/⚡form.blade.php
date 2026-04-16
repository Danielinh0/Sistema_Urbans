<?php

use Livewire\Component;
use App\Models\User;
use App\Models\Pais;
use App\Models\Estado;
use App\Models\CodigoPostal;
use App\Models\Colonia;
use App\Models\Calle;
use App\Models\Direccion;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;

new class extends Component
{
    #[Validate('required', message: 'El nombre es requerido.')]
    #[Validate('min:3', message: 'El nombre debe tener al menos 3 caracteres.')]
    public $name;

    #[Validate('required', message: 'El email es requerido.')]
    #[Validate('email', message: 'El email debe ser una dirección de correo válida.')]
    public $email;
    
    #[Validate('required', message: 'la sucursal es requerida.')]
    public $id_sucursal;

    #[Validate('required', message: 'El país es requerido.')]
    public $pais = null;

    #[Validate('required', message: 'El estado es requerido.')]
    public $estado = null;

    #[Validate('required', message: 'El código postal es requerido.')]
    public $codigoPostal = null;

    #[Validate('required', message: 'La colonia es requerida.')]
    public $colonia = null;

    #[Validate('required', message: 'La calle es requerida.')]
    public $calle = null;

    #[Validate('required', message: 'El número exterior es requerido.')]
    #[Validate('numeric', message: 'El número exterior debe ser un valor numérico.')]
    public $numero_exterior = null;

    #[Validate('numeric', message: 'El número interior debe ser un valor numérico.')]
    #[Validate('nullable')]
    public $numero_interior = null;

    
    public $password;
    public $password_confirmation;
    public $roles = [];

};
?>

<div>
    {{-- Knowing is not enough; we must apply. Being willing is not enough; we must do. - Leonardo da Vinci --}}
</div>