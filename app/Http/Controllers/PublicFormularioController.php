<?php

namespace App\Http\Controllers;

use App\Models\Integracion;
use App\Models\Lead;
use App\Models\Rol;
use App\Services\Crm\AutomatizacionEngine;
use Illuminate\Http\Request;

/**
 * Endpoint público (sin auth:sanctum) para el formulario "web-to-lead": el
 * tenant pega un <form> con fetch() en su landing (WordPress u otra) que
 * apunta acá, y cada envío crea un Lead real en su CRM. La autenticidad la
 * da el token en la URL (generado en Integracion.configuracion->token, ver
 * OnboardingService), no un usuario logueado -- por eso vive fuera de
 * auth:sanctum y necesita su propio CORS abierto (ver
 * App\Http\Middleware\AllowPublicFormularioCors).
 */
class PublicFormularioController extends Controller
{
    public function crearLead(Request $request, string $token)
    {
        $integracion = Integracion::where('tipo', 'sitio_web')
            ->whereJsonContains('configuracion->token', $token)
            ->where('estado', 'conectada')
            ->first();

        // No se distingue "token inválido" de "integración desconectada" en
        // la respuesta -- no hay razón para darle a quien llame información
        // de más sobre el estado interno del tenant.
        if (! $integracion) {
            return response()->json(['message' => 'Formulario no disponible.'], 404);
        }

        $data = $request->validate([
            'nombre' => 'nullable|string|max:150',
            'email' => 'nullable|email|max:200',
            'telefono' => 'nullable|string|max:20',
            'mensaje' => 'nullable|string|max:2000',
            // Honeypot: un campo que un humano nunca llena porque el <input>
            // real está oculto por CSS -- si un bot lo completa, se responde
            // éxito igual (para no delatar el honeypot) pero no se crea nada.
            '_gotcha' => 'nullable|string',
        ]);

        if (! empty($data['_gotcha'])) {
            return response()->json(['message' => 'ok']);
        }

        if (empty($data['nombre']) && empty($data['email']) && empty($data['telefono'])) {
            return response()->json(['message' => 'Escribe al menos tu nombre, email o teléfono.'], 422);
        }

        $idTenant = $integracion->id_tenant;
        $idAdmin = Rol::idsAdminTenant($idTenant)[0] ?? null;
        if (! $idAdmin) {
            return response()->json(['message' => 'Formulario no disponible.'], 404);
        }

        $lead = Lead::create([
            'id_tenant' => $idTenant,
            'id_usuario' => $idAdmin,
            'titulo' => 'Formulario web' . (! empty($data['nombre']) ? ": {$data['nombre']}" : ''),
            'nombre' => $data['nombre'] ?? null,
            'email' => $data['email'] ?? null,
            'telefono' => $data['telefono'] ?? null,
            'descripcion' => $data['mensaje'] ?? null,
            'estado' => 'nuevo',
            'fuente' => 'web',
        ]);

        app(AutomatizacionEngine::class)->disparar('lead_creado', $idTenant, ['lead' => $lead]);

        return response()->json(['message' => 'ok'], 201);
    }
}
