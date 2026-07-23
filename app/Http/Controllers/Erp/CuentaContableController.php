<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Erp\CuentaContable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CuentaContableController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            CuentaContable::where('id_tenant', $request->user()->id_tenant)
                ->orderBy('codigo')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $idTenant = $request->user()->id_tenant;

        $data = $request->validate([
            'codigo' => ['required', 'string', 'max:20', Rule::unique('erp_plan_cuentas', 'codigo')->where('id_tenant', $idTenant)],
            'nombre' => 'required|string|max:150',
            'tipo' => 'required|in:activo,pasivo,capital,ingreso,costo,gasto',
            'naturaleza' => 'required|in:deudora,acreedora',
            'id_cuenta_padre' => [
                'nullable',
                Rule::exists('erp_plan_cuentas', 'id')->where('id_tenant', $idTenant),
            ],
        ]);

        $data['id_tenant'] = $idTenant;

        return response()->json(CuentaContable::create($data), 201);
    }

    public function update(Request $request, string $id)
    {
        $idTenant = $request->user()->id_tenant;
        $cuenta = CuentaContable::where('id_tenant', $idTenant)->findOrFail($id);

        $data = $request->validate([
            'nombre' => 'sometimes|string|max:150',
            'activo' => 'sometimes|boolean',
        ]);

        $cuenta->update($data);

        return response()->json($cuenta);
    }

    public function destroy(Request $request, string $id)
    {
        $idTenant = $request->user()->id_tenant;
        $cuenta = CuentaContable::where('id_tenant', $idTenant)->findOrFail($id);

        if ($cuenta->detalles()->exists()) {
            return response()->json(['message' => 'La cuenta tiene movimientos y no se puede eliminar'], 422);
        }
        if ($cuenta->hijas()->exists()) {
            return response()->json(['message' => 'La cuenta tiene subcuentas y no se puede eliminar'], 422);
        }

        $cuenta->delete();

        return response()->json(['message' => 'Cuenta eliminada']);
    }
}
