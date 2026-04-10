<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UrbanController,
    CorridaController,
    BoletoController,
    RutaController
};




Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::prefix('urban')->name('urban.')->group(
        function () {
            Route::controller(UrbanController::class)->group(
                function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/create', 'create')->name('create');
                    Route::post('/', 'store')->name('store');
                    Route::get('/{id}', 'show')->name('show');
                    Route::get('/{id}/edit', 'edit')->name('edit');
                    Route::put('/{id}', 'update')->name('update');
                    Route::delete('/{id}', 'destroy')->name('destroy');
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
                    Route::get('/{id}', 'show')->name('show');
                    Route::get('/', 'store')->name('store');
                    Route::get('/{id}/edit', 'edit')->name('edit');
                    Route::put('/{id}', 'update')->name('update');
                    Route::delete('/{id}', 'destroy')->name('destroy');
                }
            );
        }
    );
    Route::prefix('boleto')->name('boleto.')->group(
        function () {
            Route::controller(BoletoController::class)->group(
                function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/create', 'create')->name('create');
                    Route::get('/{id}', 'show')->name('show');
                    Route::get('/', 'store')->name('store');
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
                    Route::get('/{id}', 'show')->name('show');
                    Route::get('/', 'store')->name('store');
                    Route::get('/{id}/edit', 'edit')->name('edit');
                    Route::put('/{id}', 'update')->name('update');
                    Route::delete('/{id}', 'destroy')->name('destroy');
                }
            );
        }
    );
});

require __DIR__ . '/settings.php';
