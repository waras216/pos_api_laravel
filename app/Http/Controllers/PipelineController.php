<?php

namespace App\Http\Controllers;

use App\Models\Pipeline;
use Illuminate\Http\Request;

class PipelineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response()->json(
            Pipeline::where('id_tenant', $request->user()->id_tenant)
            ->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:200',
            'activo' => 'sometimes|boolean',
        ]);

        $data['id_tenant'] = $request->user()->id_tenant;
        return response()->json(Pipeline::create($data),201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request,string $id)
    {
        return response()->json(
            Pipeline::where('id_pipeline', $id)
            ->where('id_tenant', $request->user()->id_tenant)
            ->firstOrFail()
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pipeline = Pipeline::where('id_pipeline',$id)
        ->where('id_tenant', $request->user()->id_tenant)
        ->firstOrFail();

        $pipeline->update($request->validate([
                'nombre' => 'sometimes|string|max:200',
                'activo' => 'sometimes|boolean',
        ]));

        return response()->json($pipeline);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,string $id)
    {
          $pipeline = Pipeline::where('id_pipeline',$id)
        ->where('id_tenant', $request->user()->id_tenant)
        ->firstOrFail();

        $pipeline->delete();

        return response()->json(['message' => 'Pipeline Eliminada']);
    }
}
