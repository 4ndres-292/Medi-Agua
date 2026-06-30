<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RolController extends Controller
{
    public function index(): JsonResponse
    {
        $roles = Rol::all();

        return response()->json([
            'success' => true,
            'message' => 'Lista de roles obtenida correctamente.',
            'data' => $roles,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        $rol = Rol::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Rol creado correctamente.',
            'data' => $rol,
        ], 201);
    }

    public function show(Rol $rol): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Rol obtenido correctamente.',
            'data' => $rol,
        ]);
    }

    public function update(Request $request, Rol $rol): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $rol->id,
        ]);

        $rol->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Rol actualizado correctamente.',
            'data' => $rol,
        ]);
    }

    public function destroy(Rol $rol): JsonResponse
    {
        $rol->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rol eliminado correctamente.',
        ], 204);
    }
}
