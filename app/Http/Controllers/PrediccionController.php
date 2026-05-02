<?php

namespace App\Http\Controllers;

use App\Models\Prediccion;
use App\Models\Ruta;
use App\Models\Urban;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PrediccionController extends Controller
{
    public function index()
    {
        $rutas = Ruta::orderBy('nombre')->get();
        $urbans = Urban::orderBy('codigo_urban')->get();
        $predicciones = Prediccion::with(['ruta', 'urban', 'usuario'])
            ->latest('created_at')
            ->take(20)
            ->get();

        return view('prediccion.index', compact('rutas', 'urbans', 'predicciones'));
    }

    public function predecir(Request $request)
    {
        $request->validate([
            'id_ruta' => 'required|exists:ruta,id_ruta',
            'id_urban' => 'required|exists:urban,id_urban',
            'fecha_salida' => 'required|date',
            'hora_salida' => 'required',
            'es_festivo' => 'nullable|boolean',
        ]);

        $fecha = Carbon::parse($request->fecha_salida);
        $hora = (int) explode(':', $request->hora_salida)[0];
        //Asi se manejan los dias de la semana 0=Lunes ... 6=Domingo
        $diaSemana = $fecha->dayOfWeekIso - 1; 
        $esFinde = $diaSemana >= 5;

        $payload = [
            'id_ruta' => (int) $request->id_ruta,
            'id_urban' => (int) $request->id_urban,
            'dia_semana' => $diaSemana,
            'hora' => $hora,
            'mes' => $fecha->month,
            'dia_mes' => $fecha->day,
            'es_festivo' => (int) ($request->boolean('es_festivo')),
            'es_finde' => (int) $esFinde,
        ];

        try {
            //llamamos a nuestra api por medio de localhost de mientras en lo que subo el proyecto a heroku, ya que en la maquina solo tengo el servidor local

            $response = Http::timeout(30)->post('http://localhost:5000/predecir', $payload);

            if (! $response->successful()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error de la API: ' . $response->body(),
                ], 502);
            }

            $data = $response->json();

            $prediccion = Prediccion::create([
                'id_ruta' => $request->id_ruta,
                'id_urban' => $request->id_urban,
                'fecha_salida' => $request->fecha_salida,
                'hora_salida' => $request->hora_salida,
                'dia_semana' => $payload['dia_semana'],
                'mes' => $payload['mes'],
                'dia_mes' => $payload['dia_mes'],
                'es_festivo' => $payload['es_festivo'],
                'es_finde' => $payload['es_finde'],
                'boletos_estimados' => $data['boletos_estimados'],
                'modelo_version' => $data['modelo_version'],
                'r2_modelo' => $data['r2_entrenamiento'],
                'id_usuario' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $data,
                'prediccion_id' => $prediccion->id_prediccion,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'No se pudo conectar con la API de predicción. Verifica que esté ejecutándose.',
            ], 500);
        }
    }

    public function estado()
    {
        try {
            $response = Http::timeout(10)->get('http://localhost:5000/estado');

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json(),
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'API no disponible',
            ], 502);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'No se pudo conectar con la API',
            ], 500);
        }
    }
}
