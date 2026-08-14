<?php

namespace App\Services\Erp;

use App\Models\Erp\Factura;
use App\Models\Erp\Pedido;
use App\Models\Tenant;
use App\Notifications\FacturaEmitidaNotification;
use App\Services\Erp\Pac\PacDriverInterface;
use App\Services\Erp\Pac\PacException;
use App\Services\IntegracionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class FacturaService
{
    public function __construct(private PacDriverInterface $pac) {}

    /**
     * @param  array{tipo:string, rfc_receptor:string, razon_social_receptor:string, uso_cfdi?:string|null, forma_pago_sat?:string|null, metodo_pago_sat?:string|null, serie?:string}  $data
     */
    public function crear(Pedido $pedido, array $data, ?int $idUsuario): Factura
    {
        if ($pedido->estado !== 'facturado') {
            throw ValidationException::withMessages([
                'id_pedido' => 'El pedido debe estar facturado (con pagos registrados) antes de emitir el comprobante fiscal.',
            ]);
        }

        if ($data['tipo'] === 'timbrada') {
            $tenant = Tenant::with('plan')->where('id_tenant', $pedido->id_tenant)->firstOrFail();

            if (! $tenant->plan?->incluye_facturacion_real) {
                throw ValidationException::withMessages([
                    'tipo' => 'El timbrado real de CFDI no está incluido en tu plan actual. Actualiza tu plan para poder emitir facturas timbradas.',
                ]);
            }

            if (! $tenant->rfc_emisor || ! $tenant->regimen_fiscal_emisor || ! $tenant->codigo_postal_emisor) {
                throw ValidationException::withMessages([
                    'tipo' => 'Faltan datos fiscales del emisor (RFC, régimen fiscal y/o código postal). Complétalos en Configuración → Fiscal antes de timbrar CFDI reales.',
                ]);
            }
        }

        $factura = DB::transaction(function () use ($pedido, $data, $idUsuario) {
            $serie = $data['serie'] ?? 'A';
            $folio = (int) (Factura::where('id_tenant', $pedido->id_tenant)
                ->where('serie', $serie)
                ->max('folio')) + 1;

            $subtotal = round((float) $pedido->total / 1.16, 2);
            $iva = round((float) $pedido->total - $subtotal, 2);

            return Factura::create([
                'id_tenant' => $pedido->id_tenant,
                'id_pedido' => $pedido->id,
                'id_usuario' => $idUsuario,
                'tipo' => $data['tipo'],
                'estado' => $data['tipo'] === 'interna' ? 'registrada' : 'pendiente_timbrado',
                'serie' => $serie,
                'folio' => $folio,
                'rfc_receptor' => $data['rfc_receptor'],
                'razon_social_receptor' => $data['razon_social_receptor'],
                'uso_cfdi' => $data['uso_cfdi'] ?? null,
                'forma_pago_sat' => $data['forma_pago_sat'] ?? null,
                'metodo_pago_sat' => $data['metodo_pago_sat'] ?? null,
                'subtotal' => $subtotal,
                'iva' => $iva,
                'total' => $pedido->total,
            ]);
        });

        // "timbrada" recién se notifica cuando timbrar() confirme el CFDI de
        // verdad -- acá solo queda "pendiente_timbrado", nada que avisarle al
        // cliente todavía.
        if ($factura->tipo === 'interna') {
            $this->notificarCliente($factura, $pedido);
        }

        return $factura;
    }

    private function notificarCliente(Factura $factura, Pedido $pedido): void
    {
        $email = $pedido->cliente?->email;
        if (! $email || ! IntegracionService::conectada($factura->id_tenant, 'email')) {
            return;
        }

        Notification::route('mail', $email)->notify(new FacturaEmitidaNotification($factura));
    }

    public function timbrar(Factura $factura): Factura
    {
        if ($factura->tipo !== 'timbrada') {
            throw ValidationException::withMessages([
                'tipo' => 'Solo las facturas de tipo "timbrada" pueden enviarse a timbrar.',
            ]);
        }

        if (! in_array($factura->estado, ['pendiente_timbrado', 'error'], true)) {
            throw ValidationException::withMessages([
                'estado' => 'Esta factura no está pendiente de timbrado.',
            ]);
        }

        try {
            $resultado = $this->pac->timbrar($factura);
        } catch (PacException $e) {
            $factura->update(['estado' => 'error', 'error_mensaje' => $e->getMessage()]);

            throw ValidationException::withMessages(['pac' => $e->getMessage()]);
        }

        $factura->update([
            'estado' => 'timbrada',
            'uuid' => $resultado->uuid,
            'xml_path' => $resultado->xmlPath,
            'pdf_path' => $resultado->pdfPath,
            'fecha_timbrado' => now(),
            'error_mensaje' => null,
        ]);

        $factura->load('pedido.cliente');
        $this->notificarCliente($factura, $factura->pedido);

        return $factura;
    }

    public function cancelar(Factura $factura): Factura
    {
        if (! in_array($factura->estado, ['registrada', 'pendiente_timbrado', 'error'], true)) {
            throw ValidationException::withMessages([
                'estado' => $factura->estado === 'timbrada'
                    ? 'Una factura ya timbrada no se puede cancelar aquí: requiere el proceso de cancelación de CFDI ante el SAT a través del PAC.'
                    : 'Esta factura ya está cancelada.',
            ]);
        }

        $factura->update(['estado' => 'cancelada']);

        return $factura;
    }
}
