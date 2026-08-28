<?php

namespace App\Http\Controllers;

use App\Models\Integracion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IntegracionController extends Controller
{
    public function index(Request $request)
    {
        $integraciones = Integracion::where('id_tenant', $request->user()->id_tenant)->get();

        // "almacenamiento" no es una integración que el tenant prenda/apague
        // por sí sola -- el bucket es una config de toda la plataforma (ver
        // config/filesystems.php). Su estado siempre refleja si esa config
        // está realmente activa, así el toggle nunca miente.
        $almacenamiento = $integraciones->firstWhere('tipo', 'almacenamiento');
        $conectadaDeVerdad = env('FILESYSTEM_DISK_PUBLIC') === 's3' && filled(env('AWS_BUCKET'));
        if ($almacenamiento && $almacenamiento->estado !== ($conectadaDeVerdad ? 'conectada' : 'desconectada')) {
            $almacenamiento->update(['estado' => $conectadaDeVerdad ? 'conectada' : 'desconectada']);
        }

        return response()->json($integraciones);
    }

    public function toggle(Request $request, string $id)
    {
        $integracion = Integracion::where('id', $id)
            ->where('id_tenant', $request->user()->id_tenant)
            ->firstOrFail();

        if ($integracion->tipo === 'almacenamiento') {
            return response()->json([
                'message' => 'El almacenamiento en la nube se activa configurando un bucket real (ver .env: FILESYSTEM_DISK_PUBLIC), no con este interruptor.',
            ], 422);
        }

        $integracion->update([
            'estado' => $integracion->estado === 'conectada' ? 'desconectada' : 'conectada',
        ]);

        return response()->json($integracion);
    }

    /**
     * Solo para "sitio_web" por ahora: guarda la URL de la landing y el ID
     * de Google Analytics del tenant (ambos son solo de referencia, STRATO
     * no los usa para nada -- el tenant los pega en su propio WordPress).
     */
    public function actualizarConfiguracion(Request $request, string $id)
    {
        $integracion = Integracion::where('id', $id)
            ->where('id_tenant', $request->user()->id_tenant)
            ->where('tipo', 'sitio_web')
            ->firstOrFail();

        $data = $request->validate([
            'url_sitio' => 'nullable|url|max:255',
            'google_analytics_id' => 'nullable|string|max:50',
        ]);

        $integracion->update([
            'configuracion' => array_merge($integracion->configuracion ?? [], $data),
        ]);

        return response()->json($integracion);
    }

    /**
     * Rota el token del formulario público -- por si el tenant sospecha que
     * alguien más lo tiene (ej. lo compartió sin querer). Los envíos viejos
     * ya guardados en su HTML dejan de funcionar de inmediato.
     */
    public function regenerarToken(Request $request, string $id)
    {
        $integracion = Integracion::where('id', $id)
            ->where('id_tenant', $request->user()->id_tenant)
            ->where('tipo', 'sitio_web')
            ->firstOrFail();

        $integracion->update([
            'configuracion' => array_merge($integracion->configuracion ?? [], ['token' => Str::random(40)]),
        ]);

        return response()->json($integracion);
    }
}
