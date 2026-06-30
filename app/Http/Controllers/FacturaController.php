<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
            'numero'            => 'required|string|unique:facturas,numero',
            'socio_id'          => 'required|exists:socios,id',
            'lectura_id'        => 'required|exists:lecturas,id',
            'monto_total'       => 'required|numeric|min:0',
            'fecha_emision'     => 'required|date',
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha_emision',
            'estado'            => 'nullable|string|in:Pendiente,Pagada,Vencida,Anulada',
            'tarifas'           => 'required|array|min:1',
            'tarifas.*.tarifa_id'       => 'required|exists:tarifas,id',
            'tarifas.*.cantidad'        => 'required|numeric|min:0',
            'tarifas.*.precio_unitario' => 'required|numeric|min:0',
            'tarifas.*.subtotal'        => 'required|numeric|min:0',
        ]);

        $tarifas = $validated['tarifas'];
        unset($validated['tarifas']);

        $factura = Factura::create($validated);

        foreach ($tarifas as $tarifa) {
            $factura->tarifas()->attach($tarifa['tarifa_id'], [
                'cantidad'        => $tarifa['cantidad'],
                'precio_unitario' => $tarifa['precio_unitario'],
                'subtotal'        => $tarifa['subtotal'],
            ]);
        }

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
            'numero'            => 'required|string|unique:facturas,numero,' . $factura->id,
            'socio_id'          => 'required|exists:socios,id',
            'lectura_id'        => 'required|exists:lecturas,id',
            'monto_total'       => 'required|numeric|min:0',
            'fecha_emision'     => 'required|date',
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha_emision',
            'estado'            => 'nullable|string|in:Pendiente,Pagada,Vencida,Anulada',
            'tarifas'           => 'required|array|min:1',
            'tarifas.*.tarifa_id'       => 'required|exists:tarifas,id',
            'tarifas.*.cantidad'        => 'required|numeric|min:0',
            'tarifas.*.precio_unitario' => 'required|numeric|min:0',
            'tarifas.*.subtotal'        => 'required|numeric|min:0',
        ]);

        $tarifas = $validated['tarifas'];
        unset($validated['tarifas']);

        $factura->update($validated);

        $factura->tarifas()->detach();
        foreach ($tarifas as $tarifa) {
            $factura->tarifas()->attach($tarifa['tarifa_id'], [
                'cantidad'        => $tarifa['cantidad'],
                'precio_unitario' => $tarifa['precio_unitario'],
                'subtotal'        => $tarifa['subtotal'],
            ]);
        }

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
