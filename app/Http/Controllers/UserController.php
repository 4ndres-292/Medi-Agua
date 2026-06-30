<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::with('rol')->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Lista de usuarios obtenida correctamente.',
            'data' => $users,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|regex:/^[a-zA-ZñÑ\s]+$/',
            'lastname' => 'required|string|max:255|regex:/^[a-zA-ZñÑ\s]+$/',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id'  => 'required|exists:roles,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado correctamente.',
            'data' => $user->load('rol'),
        ], 201);
    }

    public function show(User $user): JsonResponse
    {
        $user->load('rol', 'lecturas');

        return response()->json([
            'success' => true,
            'message' => 'Usuario obtenido correctamente.',
            'data' => $user,
        ]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|regex:/^[a-zA-ZñÑ\s]+$/',
            'lastname' => 'required|string|max:255|regex:/^[a-zA-ZñÑ\s]+$/',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role_id'  => 'required|exists:roles,id',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado correctamente.',
            'data' => $user->load('rol'),
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado correctamente.',
        ], 204);
    }
}
