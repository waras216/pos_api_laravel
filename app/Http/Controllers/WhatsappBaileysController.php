<?php

namespace App\Http\Controllers;

use App\Models\Integracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Proxy entre el frontend y whatsapp_baileys_service/ (Node interno, sin
 * puerto publicado). El estado real de conexión vive en el sidecar (la
 * sesión de WhatsApp Web en sí); acá solo se refleja en
 * Integracion.estado para que el resto de la UI (badges, gating de envío
 * en AutomatizacionEngine) lo lea de un solo lugar.
 */
class WhatsappBaileysController extends Controller
{
    private function baseUrl(): string
    {
        return config('whatsapp.drivers.baileys.url');
    }

    public function iniciar(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $respuesta = Http::baseUrl($this->baseUrl())->timeout(15)->post("/sesiones/{$idTenant}/iniciar");

        if ($respuesta->failed()) {
            return response()->json(['message' => 'No se pudo iniciar la sesión de WhatsApp. Verifica que el servicio esté corriendo.'], 502);
        }

        $this->sincronizarEstado($idTenant, $respuesta->json('status'));

        return response()->json($respuesta->json());
    }

    public function estado(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $respuesta = Http::baseUrl($this->baseUrl())->timeout(10)->get("/sesiones/{$idTenant}/estado");

        if ($respuesta->failed()) {
            return response()->json(['status' => 'error', 'qr' => null]);
        }

        $this->sincronizarEstado($idTenant, $respuesta->json('status'));

        return response()->json($respuesta->json());
    }

    public function desconectar(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        Http::baseUrl($this->baseUrl())->timeout(15)->delete("/sesiones/{$idTenant}");

        Integracion::where('id_tenant', $idTenant)->where('tipo', 'whatsapp')->update(['estado' => 'desconectada']);

        return response()->json(['message' => 'Desconectado']);
    }

    /**
     * El sidecar tiene más estados (iniciando, esperando_qr, ...) de los
     * que Integracion.estado puede guardar (enum: conectada/desconectada/
     * error) -- acá solo importa si ya terminó de conectar o no.
     */
    private function sincronizarEstado(int $idTenant, ?string $statusSidecar): void
    {
        $estadoReal = $statusSidecar === 'conectado' ? 'conectada' : 'desconectada';

        Integracion::where('id_tenant', $idTenant)->where('tipo', 'whatsapp')
            ->where('estado', '!=', $estadoReal)
            ->update(['estado' => $estadoReal]);
    }
}
