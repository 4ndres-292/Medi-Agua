<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class RoleController extends Controller
{
    /**
     * Listar todos los roles.
     */
    public function index(): JsonResponse
    {
        $roles = Role::all();

        return response()->json($roles);
    }

    /**
     * Crear un nuevo rol.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        $role = Role::create($validated);

        return response()->json($role, 201);
    }

    /**
     * Mostrar un rol específico (con sus usuarios).
     */
    public function show(Role $role): JsonResponse
    {
        $role->load('users');

        return response()->json($role);
    }

    /**
     * Actualizar un rol existente.
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
        ]);

        $role->update($validated);

        return response()->json($role);
    }

    /**
     * Eliminar un rol.
     */
    public function destroy(Role $role): JsonResponse
    {
        $role->delete();

        return response()->json(null, 204);
    }
}