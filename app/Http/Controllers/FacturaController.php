<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Tarifa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FacturaController extends Controller
{
    public function index(): JsonResponse
    {
        $facturas = Factura::with('socio', 'lectura', 'tarifas', 'pagos')->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Lista de facturas obtenida correctamente.',
            'data' => $facturas,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'socio_id'          => 'required|exists:socios,id',
            'lectura_id'        => 'required|exists:lecturas,id',
            'fecha_vencimiento' => 'required|date|after_or_equal:today',
            'tarifas'                    => 'required|array|min:1',
            'tarifas.*.tarifa_id'        => 'required|exists:tarifas,id',
            'tarifas.*.cantidad'         => 'required|numeric|min:0.01',
        ]);

        // Genera un número de factura correlativo simple (000001, 000002...)
        $ultimoId = Factura::max('id') ?? 0;
        $numero = str_pad($ultimoId + 1, 6, '0', STR_PAD_LEFT);

        $montoTotal = 0;
        $detalle = [];

        foreach ($validated['tarifas'] as $item) {
            $tarifa = Tarifa::findOrFail($item['tarifa_id']);
            $subtotal = $tarifa->precio * $item['cantidad'];
            $montoTotal += $subtotal;

            $detalle[$item['tarifa_id']] = [
                'cantidad'        => $item['cantidad'],
                'precio_unitario' => $tarifa->precio,
                'subtotal'        => $subtotal,
            ];
        }

        $factura = Factura::create([
            'numero'            => $numero,
            'socio_id'          => $validated['socio_id'],
            'lectura_id'        => $validated['lectura_id'],
            'monto_total'       => $montoTotal,
            'fecha_emision'     => now()->toDateString(),
            'fecha_vencimiento' => $validated['fecha_vencimiento'],
            'estado'            => 'Pendiente',
        ]);

        $factura->tarifas()->attach($detalle);
        $factura->load('socio', 'lectura', 'tarifas');

        return response()->json([
            'success' => true,
            'message' => 'Factura creada correctamente.',
            'data' => $factura,
        ], 201);
    }

    public function show(Factura $factura): JsonResponse
    {
        $factura->load('socio', 'lectura.medidor', 'tarifas', 'pagos');

        return response()->json([
            'success' => true,
            'message' => 'Factura obtenida correctamente.',
            'data' => $factura,
        ]);
    }

    public function update(Request $request, Factura $factura): JsonResponse
    {
        $validated = $request->validate([
            'estado' => 'required|string|in:Pendiente,Pagada,Vencida,Anulada',
        ]);

        $factura->update($validated);
        $factura->load('socio', 'lectura', 'tarifas');

        return response()->json([
            'success' => true,
            'message' => 'Factura actualizada correctamente.',
            'data' => $factura,
        ]);
    }

    public function destroy(Factura $factura): JsonResponse
    {
        $factura->tarifas()->detach();
        $factura->delete();

        return response()->json([
            'success' => true,
            'message' => 'Factura eliminada correctamente.',
        ], 204);
    }
}