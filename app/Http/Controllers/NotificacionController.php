<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response()->json(
            Notificacion::where('id_tenant', $request->user()->id_tenant)
            ->where('id_usuario', $request->user()->id_usuario)
            ->latest()
            ->take(50)
            ->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:200',
            'mensaje' => 'required|string',
            'tipo' => 'sometimes|string|max:50',
            'url' => 'nullable|string|max:500',
        ]); 

        $data['id_tenant'] = $request->user()->id_tenant;
        $data['id_usuario'] = $request->user()->id_usuario;
        $data['leida'] = false;
        $data['fecha_envio'] = now();

        $notificacion = Notificacion::create($data);

        return response()->json($notificacion,201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request,string $id)
    {
        return response()->json(
            Notificacion::where('id_notificacion', $id)
            ->where('id_usuario', $request->user()->id_usuario)
            ->firstOrFail()
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $notificacion = Notificacion::where('id_notificacion', $id)
        ->where('id_usuario', $request->user()->id_usuario)
        ->firstOrFail();

        $data = $request->validate([
            'titulo' => 'sometimes|string|max:200',
            'mensaje' => 'sometimes|string',
            'tipo' => 'sometimes|string|max:50',
            'url' => 'nullable|string|max:500',
            'leida' => 'sometimes|boolean',
        ]);

        $notificacion->update($data);

        return response()->json($notificacion);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,string $id)
    {
        $notificacion = Notificacion::where('id_notificacion', $id)
            ->where('id_usuario', $request->user()->id_usuario)
            ->firstOrFail();

        $notificacion->delete();
        
        return response()->json(['message' => 'Notificación eliminada']);
    }

    public function marcaLeida(Request $request, string $id)
    {
        $notificacion = Notificacion::where('id_notificacion',$id)
        ->where('id_usuario', $request->user()->id_usuario)
        ->firstOrFail();

        $notificacion->update(['leida' => true]);
        return response()->json($notificacion);
    }

    public function marcarTodasLeidas(Request $request)
    {
        Notificacion::where('id_tenat', $request->user()->id_tenant)
            ->where('id_usuario', $request->user()->id_usuario)
            ->where('leida', false)
            ->update(['leida' => true]);
            
         return response()->json(['message' => 'Todas las notificaciones marcadas como leídas']);   
    }
}
