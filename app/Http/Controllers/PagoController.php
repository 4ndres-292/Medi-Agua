<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    public function index(): JsonResponse
    {
        $pagos = Pago::with('factura.socio')->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Lista de pagos obtenida correctamente.',
            'data' => $pagos,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'factura_id'    => 'required|exists:facturas,id',
            'monto'         => 'required|numeric|min:0',
            'metodo_pago'   => 'required|string|in:Efectivo,QR,Transferencia,Tarjeta',
            'referencia_qr' => 'nullable|string|max:255',
            'fecha_pago'    => 'required|date',
        ]);

        $pago = Pago::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pago registrado correctamente.',
            'data' => $pago->load('factura.socio'),
        ], 201);
    }

    public function show(Pago $pago): JsonResponse
    {
        $pago->load('factura.socio');

        return response()->json([
            'success' => true,
            'message' => 'Pago obtenido correctamente.',
            'data' => $pago,
        ]);
    }

    public function update(Request $request, Pago $pago): JsonResponse
    {
        $validated = $request->validate([
            'factura_id'    => 'required|exists:facturas,id',
            'monto'         => 'required|numeric|min:0',
            'metodo_pago'   => 'required|string|in:Efectivo,QR,Transferencia,Tarjeta',
            'referencia_qr' => 'nullable|string|max:255',
            'fecha_pago'    => 'required|date',
        ]);

        $pago->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pago actualizado correctamente.',
            'data' => $pago->load('factura.socio'),
        ]);
    }

    public function destroy(Pago $pago): JsonResponse
    {
        $pago->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pago eliminado correctamente.',
        ], 204);
    }
}
