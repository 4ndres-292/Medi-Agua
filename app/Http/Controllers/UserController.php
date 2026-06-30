<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Listar todos los usuarios (con su rol).
     */
    public function index(): JsonResponse
    {
        $users = User::with('role')->get();

        return response()->json($users);
    }

    /**
     * Crear un nuevo usuario.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'lastname' => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id'  => 'nullable|exists:roles,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        $user->load('role');

        return response()->json($user, 201);
    }

    /**
     * Mostrar un usuario específico (con su rol).
     */
    public function show(User $user): JsonResponse
    {
        $user->load('role');

        return response()->json($user);
    }

    /**
     * Actualizar un usuario existente.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'username' => 'sometimes|required|string|max:255|unique:users,username,' . $user->id,
            'lastname' => 'sometimes|required|string|max:255',
            'email'    => 'sometimes|required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|nullable|string|min:6',
            'role_id'  => 'sometimes|nullable|exists:roles,id',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        $user->load('role');

        return response()->json($user);
    }

    /**
     * Eliminar un usuario.
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(null, 204);
    }
}