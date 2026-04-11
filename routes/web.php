<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AsientoController,
    BoletoController,
    ClienteController,
    CorridaController,
    RutaController,
    SocioController,
    UrbanController,
    UserController,
    VentaController,
    SucursalController
};

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::prefix('asiento')->group(function () {
        Route::controller(AsientoController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::get('/store', 'store')->name('store');
            Route::get('/{id}', 'show')->name('show');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::get('/{id}', 'update')->name('update');
            Route::get('/{id}', 'destroy')->name('destroy');
        });
    });

    Route::prefix('boleto')->name('boleto.')->group(
        function () {
            Route::controller(BoletoController::class)->group(
                function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/create', 'create')->name('create');
                    Route::get('/store', 'store')->name('store');
                    Route::get('/{id}', 'show')->name('show');
                    Route::get('/{id}/edit', 'edit')->name('edit');
                    Route::get('/{id}', 'update')->name('update');
                    Route::get('/{id}', 'destroy')->name('destroy');
                }
            );
        }
    );

    Route::prefix('cliente')->name('cliente.')->group(
        function () {
            Route::controller(ClienteController::class)->group(
                function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/create', 'create')->name('create');
                    Route::get('/store', 'store')->name('store');
                    Route::get('/{id}', 'show')->name('show');
                    Route::get('/{id}/edit', 'edit')->name('edit');
                    Route::get('/{id}', 'update')->name('update');
                    Route::get('/{id}', 'destroy')->name('destroy');
                }
            );
        }
    );

    Route::prefix('corrida')->name('corrida.')->group(
        function () {
            Route::controller(CorridaController::class)->group(
                function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/create', 'create')->name('create');
                    Route::get('/store', 'store')->name('store');
                    Route::get('/{id}', 'show')->name('show');
                    Route::get('/{id}/edit', 'edit')->name('edit');
                    Route::get('/{id}', 'update')->name('update');
                    Route::get('/{id}', 'destroy')->name('destroy');
                }
            );
        }
    );

    Route::prefix('ruta')->name('ruta.')->group(
        function () {
            Route::controller(RutaController::class)->group(
                function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/create', 'create')->name('create');
                    Route::get('/store', 'store')->name('store');
                    Route::get('/{id}', 'show')->name('show');
                    Route::get('/{id}/edit', 'edit')->name('edit');
                    Route::get('/{id}', 'update')->name('update');
                    Route::get('/{id}', 'destroy')->name('destroy');
                }
            );
        }
    );

    Route::prefix('socio')->name('socio.')->group(
        function () {
            Route::controller(SocioController::class)->group(
            function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/create', 'create')->name('create');
                    Route::get('/store', 'store')->name('store');
                    Route::get('/{id}', 'show')->name('show');
                    Route::get('/{id}/edit', 'edit')->name('edit');
                    Route::get('/{id}', 'update')->name('update');
                    Route::get('/{id}', 'destroy')->name('destroy');
                }
            );
        }
    );
    Route::prefix('sucursal')->name('sucursal.')->group(
        function () {
            Route::controller(SucursalController::class)->group(
                function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/create', 'create')->name('create');
                    Route::get('/store', 'store')->name('store');
                    Route::get('/{id}', 'show')->name('show');
                    Route::get('/{id}/edit', 'edit')->name('edit');
                    Route::get('/{id}', 'update')->name('update');
                    Route::get('/{id}', 'destroy')->name('destroy');
                }
            );
        }
    );

    Route::prefix('urban')->name('urban.')->group(
        function () {
            Route::controller(UrbanController::class)->group(
                function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/create', 'create')->name('create');
                    Route::get('/store', 'store')->name('store');
                    Route::get('/{id}', 'show')->name('show');
                    Route::get('/{id}/edit', 'edit')->name('edit');
                    Route::get('/{id}', 'update')->name('update');
                    Route::get('/{id}', 'destroy')->name('destroy');
                }
            );
        }
    );

    Route::prefix('usuario')->name('usuario.')->group(
        function () {
            Route::controller(UserController::class)->group(
                function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/create', 'create')->name('create');
                    Route::get('/store', 'store')->name('store');
                    Route::get('/{id}', 'show')->name('show');
                    Route::get('/{id}/edit', 'edit')->name('edit');
                    Route::get('/{id}', 'update')->name('update');
                    Route::get('/{id}', 'destroy')->name('destroy');
                }
            );
        }
    );

    Route::prefix('venta')->name('venta.')->group(
        function () {
            Route::controller(VentaController::class)->group(
                function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/create', 'create')->name('create');
                    Route::get('/store', 'store')->name('store');
                    Route::get('/{id}', 'show')->name('show');
                    Route::get('/{id}/edit', 'edit')->name('edit');
                    Route::get('/{id}', 'update')->name('update');
                    Route::get('/{id}', 'destroy')->name('destroy');
                }
            );
        }
    );
});

require __DIR__ . '/settings.php';
