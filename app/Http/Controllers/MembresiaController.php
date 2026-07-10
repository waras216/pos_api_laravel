<?php

namespace App\Http\Controllers;

use App\Models\Membresia;
use Illuminate\Http\Request;

class MembresiaController extends Controller
{
    /**
     * Empresas activas a las que pertenece el usuario autenticado.
     */
    public function index(Request $request)
    {
        $membresias = Membresia::where('membresias.id_usuario', $request->user()->id_usuario)
            ->where('membresias.estado', 'activa')
            ->join('tenants', 'tenants.id_tenant', '=', 'membresias.id_tenant')
            ->orderBy('tenants.nombre_tenant')
            ->get([
                'membresias.id_tenant',
                'tenants.nombre_tenant as empresa',
                'membresias.es_owner',
            ]);

        return response()->json($membresias);
    }

    /**
     * Cambia la empresa activa del usuario autenticado.
     */
    public function activar(Request $request, string $idTenant)
    {
        $membresia = Membresia::where('id_usuario', $request->user()->id_usuario)
            ->where('id_tenant', $idTenant)
            ->where('estado', 'activa')
            ->first();

        if (! $membresia) {
            return response()->json(['message' => 'No perteneces a esa empresa'], 403);
        }

        $request->user()->update(['id_tenant' => $idTenant]);

        $auth = new AuthController();
        return response()->json($auth->serializeUser($request->user()->fresh()));
    }
}
