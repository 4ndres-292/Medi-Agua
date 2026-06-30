<?php

namespace App\Http\Controllers;

use App\Models\Lectura;
use App\Models\Medidor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LecturaController extends Controller
{
    public function index(): JsonResponse
    {
        $lecturas = Lectura::with('medidor.socio', 'usuario')->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Lista de lecturas obtenida correctamente.',
            'data' => $lecturas,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'medidor_id'       => 'required|exists:medidores,id',
            'lectura_anterior' => 'required|numeric|min:0',
            'lectura_actual'   => 'required|numeric|gte:lectura_anterior',
            'observacion'      => 'nullable|string',
            'usuario_id'       => 'required|exists:users,id',
            'fecha_lectura'    => 'required|date',
        ]);

        $validated['consumo'] = $validated['lectura_actual'] - $validated['lectura_anterior'];

        $lectura = Lectura::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Lectura registrada correctamente.',
            'data' => $lectura->load('medidor.socio', 'usuario'),
        ], 201);
    }

    public function show(Lectura $lectura): JsonResponse
    {
        $lectura->load('medidor.socio', 'usuario', 'factura');

        return response()->json([
            'success' => true,
            'message' => 'Lectura obtenida correctamente.',
            'data' => $lectura,
        ]);
    }

    public function update(Request $request, Lectura $lectura): JsonResponse
    {
        $validated = $request->validate([
            'medidor_id'       => 'required|exists:medidores,id',
            'lectura_anterior' => 'required|numeric|min:0',
            'lectura_actual'   => 'required|numeric|gte:lectura_anterior',
            'observacion'      => 'nullable|string',
            'usuario_id'       => 'required|exists:users,id',
            'fecha_lectura'    => 'required|date',
        ]);

        $validated['consumo'] = $validated['lectura_actual'] - $validated['lectura_anterior'];

        $lectura->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Lectura actualizada correctamente.',
            'data' => $lectura->load('medidor.socio', 'usuario'),
        ]);
    }

    public function destroy(Lectura $lectura): JsonResponse
    {
        $lectura->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lectura eliminada correctamente.',
        ], 204);
    }
}
