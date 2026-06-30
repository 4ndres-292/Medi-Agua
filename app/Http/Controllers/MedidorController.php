<?php

namespace App\Http\Controllers;

use App\Models\Medidor;
use App\Models\Socio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedidorController extends Controller
{
    public function index(): JsonResponse
    {
        $medidores = Medidor::with('socio')->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Lista de medidores obtenida correctamente.',
            'data' => $medidores,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'codigo'    => 'required|string|unique:medidores,codigo',
            'ubicacion' => 'required|string|max:255',
            'socio_id'  => 'required|exists:socios,id',
            'estado'    => 'required|string|in:activo,inactivo',
        ]);

        $medidor = Medidor::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Medidor creado correctamente.',
            'data' => $medidor->load('socio'),
        ], 201);
    }

    public function show(Medidor $medidor): JsonResponse
    {
        $medidor->load('socio', 'lecturas');

        return response()->json([
            'success' => true,
            'message' => 'Medidor obtenido correctamente.',
            'data' => $medidor,
        ]);
    }

    public function update(Request $request, Medidor $medidor): JsonResponse
    {
        $validated = $request->validate([
            'codigo'    => 'required|string|unique:medidores,codigo,' . $medidor->id,
            'ubicacion' => 'required|string|max:255',
            'socio_id'  => 'required|exists:socios,id',
            'estado'    => 'required|string|in:activo,inactivo',
        ]);

        $medidor->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Medidor actualizado correctamente.',
            'data' => $medidor->load('socio'),
        ]);
    }

    public function destroy(Medidor $medidor): JsonResponse
    {
        $medidor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Medidor eliminado correctamente.',
        ], 204);
    }
}
