<?php

use App\Http\Controllers\{
    AsientoController,
    BoletoController,
    ClienteController,
    CorridaController,
    PrediccionController,
    RutaController,
    SocioController,
    UrbanController,
    UserController,
    VentaController,
    SucursalController,
    DashboardController,
    TurnoController,
    BoletoYBitacoraController
};
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/turno/abrir', function () {
        return view('turno.create');
    })->name('turno.create');
    Route::post('/turno/cerrar', [TurnoController::class, 'close'])->name('turno.close');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/mi-corrida', function () {
        return view('chofer.detalle-prox-corrida');
    })->name('chofer.mi-corrida')->middleware(['auth', 'verified', 'role:chofer']);

    // Sin middleware role, verificando manualmente
    Route::get('/taquillas', function () {
        if (!auth()->user()->hasAnyRole(['admin', 'gerente'])) {
            abort(403);
        }
        return view('taquillas.index');
    })->name('taquilla.index')->middleware(['auth', 'verified']);

    Route::get('/reportes', function () {
        return view('reportes.index');
    })->name('reportes.index');

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
                    Route::post('/store', 'store')->name('store');
                    Route::get('/{id}', 'show')->name('show');
                    Route::get('/{id}/edit', 'edit')->name('edit');
                    Route::put('/{id}', 'update')->name('update');
                    Route::delete('/{id}', 'destroy')->name('destroy');
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
                    Route::get('/paquetes', 'ventaPaquetes')->name('paquetes');
                    Route::get('/store', 'store')->name('store');
                    Route::get('/{id}', 'show')->name('show');
                    Route::get('/{id}/edit', 'edit')->name('edit');
                    Route::get('/{id}', 'update')->name('update');
                    Route::get('/{id}', 'destroy')->name('destroy');
                }
            );
        }
    );

    Route::prefix('prediccion')->name('prediccion.')->group(function () {
        Route::get('/', [PrediccionController::class, 'index'])->name('index');
        Route::post('/predecir', [PrediccionController::class, 'predecir'])->name('predecir');
        Route::get('/estado', [PrediccionController::class, 'estado'])->name('estado');
    });

    Route::prefix('servicios')->name('servicios.')->group(function () {
        Route::post('/boleto-cliente', [BoletoYBitacoraController::class, 'generarBoletoCliente'])->name('boleto.cliente');
        Route::post('/boleto-paquete', [BoletoYBitacoraController::class, 'generarBoletoPaquete'])->name('boleto.paquete');
        Route::get('/bitacora/{id_corrida}', [BoletoYBitacoraController::class, 'obtenerBitacora'])->name('bitacora');
        Route::get('/bitacora/{id_corrida}/pdf', [BoletoYBitacoraController::class, 'descargarBitacoraPDF'])->name('bitacora.pdf');
        Route::get('/boleto-cliente/{id_boleto}/pdf', [BoletoYBitacoraController::class, 'descargarBoletoClientePDF'])->name('boleto.cliente.pdf');
        Route::get('/boleto-paquete/{id_boleto}/pdf', [BoletoYBitacoraController::class, 'descargarBoletoPaquetePDF'])->name('boleto.paquete.pdf');
    });
});

require __DIR__.'/settings.php';
