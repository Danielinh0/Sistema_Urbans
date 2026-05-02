<x-layouts::app :title="__('Predicción de Demanda')">
    <section class="flex flex-col gap-6 px-4 pt-2">

        {{-- Encabezado --}}
        <div class="flex flex-col justify-between items-center md:flex-row">
            <x-heading :icono="'activity'" texto="Predicción de Demanda" />
        </div>

        {{-- Estado del modelo --}}
        <div id="estado-modelo" class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 p-4">
            <flux:text class="text-sm text-zinc-500">Cargando estado del modelo...</flux:text>
        </div>

        {{-- Formulario de predicción --}}
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6">
            <flux:heading class="!text-lg !font-bold mb-4" size="lg">Nueva Predicción</flux:heading>

            <form id="form-prediccion" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @csrf
                <div>
                    <flux:text class="text-sm font-medium mb-1">Ruta</flux:text>
                    <select name="id_ruta" required
                        class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm">
                        <option value="">Selecciona una ruta</option>
                        @foreach($rutas as $ruta)
                        <option value="{{ $ruta->id_ruta }}">{{ $ruta->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <flux:text class="text-sm font-medium mb-1">Urban (Unidad)</flux:text>
                    <select name="id_urban" required
                        class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm">
                        <option value="">Selecciona una unidad</option>
                        @foreach($urbans as $urban)
                        <option value="{{ $urban->id_urban }}">{{ $urban->codigo_urban }} — {{ $urban->placa }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <flux:text class="text-sm font-medium mb-1">Fecha de salida</flux:text>
                    <input type="date" name="fecha_salida" required
                        class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                </div>

                <div>
                    <flux:text class="text-sm font-medium mb-1">Hora de salida</flux:text>
                    <input type="time" name="hora_salida" required
                        class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-3 py-2 text-sm" />
                </div>

                <div class="flex items-center gap-2 pt-5">
                    <input type="checkbox" name="es_festivo" id="es_festivo" value="1"
                        class="rounded border-zinc-300 dark:border-zinc-600 text-azul_menu" />
                    <label for="es_festivo" class="text-sm">Es día festivo</label>
                </div>

                <div class="flex items-end">
                    <flux:button type="submit" icon="activity"
                        class="bg-azul_menu! cursor-pointer text-white! hover:bg-azul_rebajado!
                        hover:text-azul_menu! transition delay-150 duration-300 ease-in-out w-full justify-center">
                        Predecir
                    </flux:button>
                </div>
            </form>
        </div>

        {{-- Resultado de la predicción --}}
        <div id="resultado-prediccion" class="hidden">
            <div class="rounded-xl border-2 border-green-400 dark:border-green-600 bg-green-50 dark:bg-green-900/20 p-6 text-center">
                <flux:icon name="activity" class="mx-auto mb-2 size-10 text-green-600 dark:text-green-400" />
                <flux:heading class="!text-4xl !font-bold !text-green-700 dark:!text-green-300" id="boletos-estimados">0</flux:heading>
                <flux:text class="text-lg text-green-600 dark:text-green-400 mt-1">boletos estimados</flux:text>
                <flux:text class="text-sm text-zinc-500 mt-2" id="detalle-modelo"></flux:text>
            </div>
        </div>

        {{-- Error --}}
        <div id="error-prediccion" class="hidden">
            <div class="rounded-xl border-2 border-red-400 dark:border-red-600 bg-red-50 dark:bg-red-900/20 p-6 text-center">
                <flux:icon name="x" class="mx-auto mb-2 size-10 text-red-600 dark:text-red-400" />
                <flux:text class="text-red-600 dark:text-red-400" id="error-mensaje">Error al obtener predicción</flux:text>
            </div>
        </div>

        {{-- Historial --}}
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-6">
            <flux:heading class="!text-lg !font-bold mb-4" size="lg">Historial de Predicciones</flux:heading>

            @if($predicciones->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase text-zinc-500 dark:text-zinc-400 border-b border-zinc-200 dark:border-zinc-700">
                        <tr>
                            <th class="px-4 py-3">Fecha</th>
                            <th class="px-4 py-3">Ruta</th>
                            <th class="px-4 py-3">Unidad</th>
                            <th class="px-4 py-3">Boletos Est.</th>
                            <th class="px-4 py-3">Modelo</th>
                            <th class="px-4 py-3">Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($predicciones as $p)
                        <tr class="border-b border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="px-4 py-3">{{ $p->fecha_salida?->format('d/m/Y') }} {{ $p->hora_salida ? Carbon\Carbon::parse($p->hora_salida)->format('H:i') : '' }}</td>
                            <td class="px-4 py-3">{{ $p->ruta?->nombre ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $p->urban?->codigo_urban ?? '—' }}</td>
                            <td class="px-4 py-3 font-bold text-azul_menu">{{ $p->boletos_estimados }}</td>
                            <td class="px-4 py-3 text-zinc-500">{{ $p->modelo_version }}</td>
                            <td class="px-4 py-3">{{ $p->usuario?->name ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <flux:text class="text-zinc-400 text-center py-8">No hay predicciones registradas aún.</flux:text>
            @endif
        </div>

    </section>

    {{-- JavaScript para interacciones --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Cargar estado del modelo
            fetch('{{ route('prediccion.estado') }}')
                .then(r => r.json())
                .then(res => {
                    const el = document.getElementById('estado-modelo');
                    if (res.success) {
                        const d = res.data;
                        const color = d.necesita_actualizacion ? 'text-yellow-600' : 'text-green-600';
                        el.innerHTML = `
                            <div class="flex flex-wrap gap-6 items-center">
                                <div>
                                    <span class="text-xs uppercase text-zinc-500">Estado</span>
                                    <p class="text-sm font-semibold ${color}">${d.advertencia}</p>
                                </div>
                                <div>
                                    <span class="text-xs uppercase text-zinc-500">R²</span>
                                    <p class="text-sm font-bold">${d.r2}</p>
                                </div>
                                <div>
                                    <span class="text-xs uppercase text-zinc-500">Entrenado</span>
                                    <p class="text-sm">${d.fecha_entrenamiento} (${d.dias_desde_entrenamiento} días)</p>
                                </div>
                                <div>
                                    <span class="text-xs uppercase text-zinc-500">Registros</span>
                                    <p class="text-sm">${d.n_registros_entrenamiento}</p>
                                </div>
                            </div>`;
                    } else {
                        el.innerHTML = `<div class="flex items-center gap-2 text-red-500"><span class="text-sm">API de predicción no disponible — inicia el servidor Flask</span></div>`;
                    }
                })
                .catch(() => {
                    document.getElementById('estado-modelo').innerHTML =
                        `<p class="text-sm text-red-500">No se pudo conectar con la API de predicción</p>`;
                });

            // Enviar predicción
            document.getElementById('form-prediccion').addEventListener('submit', async (e) => {
                e.preventDefault();
                const form = e.target;
                const btn = form.querySelector('button[type="submit"]');
                const resultadoEl = document.getElementById('resultado-prediccion');
                const errorEl = document.getElementById('error-prediccion');

                resultadoEl.classList.add('hidden');
                errorEl.classList.add('hidden');
                btn.disabled = true;
                btn.textContent = 'Calculando...';

                try {
                    const res = await fetch('{{ route('prediccion.predecir') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            id_ruta: form.id_ruta.value,
                            id_urban: form.id_urban.value,
                            fecha_salida: form.fecha_salida.value,
                            hora_salida: form.hora_salida.value,
                            es_festivo: form.es_festivo.checked ? 1 : 0,
                        }),
                    });

                    const data = await res.json();

                    if (data.success) {
                        document.getElementById('boletos-estimados').textContent = data.data.boletos_estimados;
                        document.getElementById('detalle-modelo').textContent =
                            `Modelo: ${data.data.modelo_version} · R²: ${data.data.r2_entrenamiento}`;
                        resultadoEl.classList.remove('hidden');
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        document.getElementById('error-mensaje').textContent = data.error;
                        errorEl.classList.remove('hidden');
                    }
                } catch (err) {
                    document.getElementById('error-mensaje').textContent = 'Error de conexión con el servidor';
                    errorEl.classList.remove('hidden');
                } finally {
                    btn.disabled = false;
                    btn.textContent = 'Predecir';
                }
            });
        });
    </script>
</x-layouts::app>
