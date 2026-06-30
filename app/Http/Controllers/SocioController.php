<?php

namespace App\Http\Controllers;

use App\Models\Socio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SocioController extends Controller
{
    public function index(): JsonResponse
    {
        $socios = Socio::with('medidores')->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Lista de socios obtenida correctamente.',
            'data' => $socios,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombres'   => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'ci'        => 'required|string|unique:socios,ci',
            'telefono'  => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'estado'    => 'nullable|string|in:activo,inactivo',
        ]);

        $socio = Socio::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Socio creado correctamente.',
            'data' => $socio,
        ], 201);
    }

    public function show(Socio $socio): JsonResponse
    {
        $socio->load('medidores', 'facturas', 'notificaciones');

        return response()->json([
            'success' => true,
            'message' => 'Socio obtenido correctamente.',
            'data' => $socio,
        ]);
    }

    public function update(Request $request, Socio $socio): JsonResponse
    {
        $validated = $request->validate([
            'nombres'   => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'ci'        => 'required|string|unique:socios,ci,' . $socio->id,
            'telefono'  => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'estado'    => 'nullable|string|in:activo,inactivo',
        ]);

        $socio->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Socio actualizado correctamente.',
            'data' => $socio,
        ]);
    }

    public function destroy(Socio $socio): JsonResponse
    {
        $socio->delete();

        return response()->json([
            'success' => true,
            'message' => 'Socio eliminado correctamente.',
        ], 204);
    }
}
