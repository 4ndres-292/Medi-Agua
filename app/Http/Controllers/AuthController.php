<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    ///////////////////////////////////////////////////////
    #login Alan
    ///////////////////////////////////////////////////////
    public function register(Request $request)
    {
        $user = new User();
        $user->username = $request->input('username');
        $user->lastname = $request->input('lastname');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->role_id = $request->input('role_id');
        $user->save();
        /*$validated = $request->validate([
            'username' => 'required|string|max:255|regex:/^[a-zA-ZñÑ\s]+$/',
            'lastname' => 'required|string|max:255|regex:/^[a-zA-ZñÑ\s]+$/',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id'  => 'required|exists:roles,id',
        ]);

        $user = User::create($validated);*/
        Auth::login($user);
        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado correctamente.',
            'data' => $user,
        ]);
    }
    //////////////////////////////////////////////////////////////////////
    # logout Alan
    //////////////////////////////////////////////////////////////////////
    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        /*return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada correctamente.',
        ]);*/
        return redirect('/login')->with('success', 'Sesión cerrada correctamente.');
    }
    ///////////////////////////////////////////////////////////////////////////////////////////
    # Login Alan
    ///////////////////////////////////////////////////////////////////////////////////////////
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales inválidas.',
            ], 401);
        }
        $remember = $request->boolean('remember', false);
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return response()->json([
                'success' => true,
                'message' => 'Inicio de sesión exitoso.',
                'data' => [
                    'user' => Auth::user(),
                ],
            ]);
        }
        


    }
    ///////////////////////////////////////////////////////////////////////////////////////////
    #login Andres
    /////////////////////////////////////////////////////////////////////
    /*public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::guard('web')->once($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales inválidas.',
            ], 401);
        }

        $user = Auth::user()->load('rol');

        // Elimina cualquier token anterior si deseas permitir
        // solo una sesión por dispositivo.
        // $user->tokens()->delete();

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Inicio de sesión exitoso.',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ]);
    }
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()->load('rol'),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }*/
}