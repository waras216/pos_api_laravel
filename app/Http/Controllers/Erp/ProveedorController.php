<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $query = Proveedor::where('id_tenant', $request->user()->id_tenant);

        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->whereLike('nombre', '%' . $request->search . '%');
        });

        return response()->json($query->orderBy('nombre')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'contacto' => 'nullable|string|max:150',
            'email' => 'nullable|email|max:200',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:250',
            'rfc' => 'nullable|string|max:20',
            'activo' => 'sometimes|boolean',
        ]);

        $data['id_tenant'] = $request->user()->id_tenant;

        return response()->json(Proveedor::create($data), 201);
    }

    public function show(Request $request, string $id)
    {
        return response()->json(
            Proveedor::where('id_tenant', $request->user()->id_tenant)->findOrFail($id)
        );
    }

    public function update(Request $request, string $id)
    {
        $proveedor = Proveedor::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);

        $data = $request->validate([
            'nombre' => 'sometimes|string|max:150',
            'contacto' => 'nullable|string|max:150',
            'email' => 'nullable|email|max:200',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:250',
            'rfc' => 'nullable|string|max:20',
            'activo' => 'sometimes|boolean',
        ]);

        $proveedor->update($data);

        return response()->json($proveedor);
    }

    public function destroy(Request $request, string $id)
    {
        $proveedor = Proveedor::where('id_tenant', $request->user()->id_tenant)->findOrFail($id);
        $proveedor->delete();

        return response()->json(['message' => 'Proveedor eliminado']);
    }

    public function papelera(Request $request)
    {
        return response()->json(
            Proveedor::onlyTrashed()
                ->where('id_tenant', $request->user()->id_tenant)
                ->latest('deleted_at')
                ->get()
        );
    }

    public function restaurar(Request $request, string $id)
    {
        $proveedor = Proveedor::onlyTrashed()->where('id_tenant', $request->user()->id_tenant)->findOrFail($id);
        $proveedor->restore();

        return response()->json($proveedor);
    }
}
