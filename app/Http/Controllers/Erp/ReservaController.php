<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\Estadia;
use App\Models\Erp\Habitacion;
use App\Models\Erp\Reserva;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReservaController extends Controller
{
    public function index(Request $request)
    {
        $reservas = Reserva::where('id_tenant', $request->user()->id_tenant)
            ->with('habitacion:id,numero,tipo')
            ->orderBy('fecha_checkin')
            ->get();

        return response()->json($reservas);
    }

    public function store(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $data = $request->validate([
            'id_habitacion' => [
                'required',
                Rule::exists('erp_habitaciones', 'id')->where('id_tenant', $idTenant),
            ],
            'huesped' => 'required|string|max:150',
            'id_cliente' => [
                'nullable',
                Rule::exists('clientes', 'id_cliente')->where('id_tenant', $idTenant),
            ],
            'telefono' => 'nullable|string|max:20',
            'fecha_checkin' => 'required|date|after_or_equal:today',
            'noches' => 'required|integer|min:1',
            'notas' => 'nullable|string|max:255',
        ]);

        $fechaCheckin = $data['fecha_checkin'];
        $fechaCheckout = now()->parse($fechaCheckin)->addDays($data['noches'])->toDateString();

        $habitacion = Habitacion::where('id_tenant', $idTenant)->findOrFail($data['id_habitacion']);

        if ($this->hayTraslape($idTenant, $habitacion, $fechaCheckin, $fechaCheckout)) {
            return response()->json([
                'message' => 'La habitación ya tiene una reserva o estancia que se cruza con esas fechas.',
            ], 422);
        }

        $reserva = Reserva::create([
            'id_tenant' => $idTenant,
            'id_habitacion' => $habitacion->id,
            'huesped' => $data['huesped'],
            'id_cliente' => $data['id_cliente'] ?? null,
            'telefono' => $data['telefono'] ?? null,
            'fecha_checkin' => $fechaCheckin,
            'fecha_checkout' => $fechaCheckout,
            'noches' => $data['noches'],
            'notas' => $data['notas'] ?? null,
            'estado' => 'pendiente',
        ]);

        return response()->json($reserva->load('habitacion:id,numero,tipo'), 201);
    }

    public function cancelar(Request $request, string $id)
    {
        $reserva = Reserva::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        if ($reserva->estado !== 'pendiente') {
            return response()->json(['message' => 'Solo se pueden cancelar reservas pendientes'], 422);
        }

        $reserva->update(['estado' => 'cancelada']);

        return response()->json($reserva);
    }

    public function checkIn(Request $request, string $id)
    {
        $idTenant = $request->user()->id_tenant;
        $reserva = Reserva::where('id_tenant', $idTenant)->findOrFail($id);

        if ($reserva->estado !== 'pendiente') {
            return response()->json(['message' => 'Esta reserva ya no está pendiente'], 422);
        }

        $habitacion = Habitacion::where('id_tenant', $idTenant)->findOrFail($reserva->id_habitacion);

        if ($habitacion->estado !== 'libre') {
            return response()->json(['message' => 'La habitación no está libre para hacer el check-in'], 422);
        }

        if ($habitacion->estado_limpieza !== 'limpia') {
            return response()->json(['message' => 'La habitación necesita limpieza antes del check-in'], 422);
        }

        $checkIn = now()->toDateString();
        $checkOut = now()->addDays($reserva->noches)->toDateString();

        $habitacion->update([
            'estado' => 'ocupada',
            'huesped' => $reserva->huesped,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'noches' => $reserva->noches,
        ]);

        Estadia::create([
            'id_tenant' => $idTenant,
            'id_habitacion' => $habitacion->id,
            'huesped' => $reserva->huesped,
            'id_cliente' => $reserva->id_cliente,
            'check_in' => $checkIn,
            'check_out_programado' => $checkOut,
            'noches' => $reserva->noches,
            'estado' => 'activa',
        ]);

        $reserva->update(['estado' => 'convertida']);

        return response()->json([
            'habitacion' => $habitacion->load('consumos.producto'),
            'reserva' => $reserva,
        ]);
    }

    /** Traslape con otras reservas pendientes de la misma habitación o con su estancia activa actual. */
    private function hayTraslape(int $idTenant, Habitacion $habitacion, string $fechaCheckin, string $fechaCheckout): bool
    {
        $conReservas = Reserva::where('id_tenant', $idTenant)
            ->where('id_habitacion', $habitacion->id)
            ->where('estado', 'pendiente')
            ->where('fecha_checkin', '<', $fechaCheckout)
            ->where('fecha_checkout', '>', $fechaCheckin)
            ->exists();

        if ($conReservas) {
            return true;
        }

        if ($habitacion->estado === 'ocupada' && $habitacion->check_in && $habitacion->check_out) {
            return $habitacion->check_in->toDateString() < $fechaCheckout
                && $habitacion->check_out->toDateString() > $fechaCheckin;
        }

        return false;
    }
}
