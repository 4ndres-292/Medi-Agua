<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function index(): JsonResponse
    {
        $notificaciones = Notificacion::with('socio')->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Lista de notificaciones obtenida correctamente.',
            'data' => $notificaciones,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'socio_id'    => 'required|exists:socios,id',
            'tipo'        => 'required|string|in:Factura generada,Pago registrado,Pago pendiente,Corte de servicio,Aviso general',
            'mensaje'     => 'required|string',
            'estado'      => 'nullable|string|in:enviado,pendiente,leido',
            'fecha_envio' => 'required|date',
        ]);

        $notificacion = Notificacion::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Notificación creada correctamente.',
            'data' => $notificacion->load('socio'),
        ], 201);
    }

    public function show(Notificacion $notificacion): JsonResponse
    {
        $notificacion->load('socio');

        return response()->json([
            'success' => true,
            'message' => 'Notificación obtenida correctamente.',
            'data' => $notificacion,
        ]);
    }

    public function update(Request $request, Notificacion $notificacion): JsonResponse
    {
        $validated = $request->validate([
            'socio_id'    => 'required|exists:socios,id',
            'tipo'        => 'required|string|in:Factura generada,Pago registrado,Pago pendiente,Corte de servicio,Aviso general',
            'mensaje'     => 'required|string',
            'estado'      => 'nullable|string|in:enviado,pendiente,leido',
            'fecha_envio' => 'required|date',
        ]);

        $notificacion->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Notificación actualizada correctamente.',
            'data' => $notificacion->load('socio'),
        ]);
    }

    public function destroy(Notificacion $notificacion): JsonResponse
    {
        $notificacion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notificación eliminada correctamente.',
        ], 204);
    }
}
