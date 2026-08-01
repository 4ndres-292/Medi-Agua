<?php

namespace App\Http\Controllers;

use App\Models\QrPago;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QrPagoController extends Controller
{
    public function show(): JsonResponse
    {
        $qr = QrPago::latest('fecha_actualizacion')->first();

        if (!$qr) {
            return response()->json([
                'success' => false,
                'message' => 'Aún no se ha subido ningún QR de pago.',
            ], 404);
        }

        $vencido = $qr->valido_hasta && now()->toDateString() > $qr->valido_hasta;

        return response()->json([
            'success' => true,
            'message' => 'QR de pago obtenido correctamente.',
            'data' => [
                'id'                  => $qr->id,
                'imagen_url'          => asset('storage/' . $qr->imagen),
                'fecha_actualizacion' => $qr->fecha_actualizacion,
                'valido_hasta'        => $qr->valido_hasta,
                'vencido'             => $vencido,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'imagen'          => 'required|image|max:4096',
            'valido_hasta'    => 'nullable|date|after:today',
            'actualizado_por' => 'nullable|exists:users,id',
        ]);

        $path = $request->file('imagen')->store('qr', 'public');

        // Mantiene un solo QR vigente: borra el anterior (archivo + registro)
        foreach (QrPago::all() as $anterior) {
            Storage::disk('public')->delete($anterior->imagen);
            $anterior->delete();
        }

        $qr = QrPago::create([
            'imagen'              => $path,
            'fecha_actualizacion' => now()->toDateString(),
            'valido_hasta'        => $validated['valido_hasta'] ?? null,
            'actualizado_por'     => $validated['actualizado_por'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'QR de pago actualizado correctamente.',
            'data' => [
                'id'                  => $qr->id,
                'imagen_url'          => asset('storage/' . $qr->imagen),
                'fecha_actualizacion' => $qr->fecha_actualizacion,
                'valido_hasta'        => $qr->valido_hasta,
            ],
        ], 201);
    }
}