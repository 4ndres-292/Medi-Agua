<?php

namespace App\Http\Controllers;

use App\Models\Tarifa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TarifaController extends Controller
{
    public function index(): JsonResponse
    {
        $tarifas = Tarifa::all();

        return response()->json([
            'success' => true,
            'message' => 'Lista de tarifas obtenida correctamente.',
            'data' => $tarifas,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre'  => 'required|string|max:255|unique:tarifas,nombre',
            'precio'  => 'required|numeric|min:0',
        ]);

        $tarifa = Tarifa::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tarifa creada correctamente.',
            'data' => $tarifa,
        ], 201);
    }

    public function show(Tarifa $tarifa): JsonResponse
    {
        $tarifa->load('facturas');

        return response()->json([
            'success' => true,
            'message' => 'Tarifa obtenida correctamente.',
            'data' => $tarifa,
        ]);
    }

    public function update(Request $request, Tarifa $tarifa): JsonResponse
    {
        $validated = $request->validate([
            'nombre'  => 'required|string|max:255|unique:tarifas,nombre,' . $tarifa->id,
            'precio'  => 'required|numeric|min:0',
        ]);

        $tarifa->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tarifa actualizada correctamente.',
            'data' => $tarifa,
        ]);
    }

    public function destroy(Tarifa $tarifa): JsonResponse
    {
        $tarifa->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tarifa eliminada correctamente.',
        ], 204);
    }
}
