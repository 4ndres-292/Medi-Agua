<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use App\Http\Resources\LoginResource;
use App\Http\Resources\UserResource;
use App\Support\ApiResponse;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;

        //$this->middleware('throttle:5,1')->only('login');
    }


    public function login(LoginRequest $request): JsonResponse
    {
        $data = $this->authService->login($request->validated());

        return ApiResponse::success(
            new LoginResource($data),
            'Inicio de sesión exitoso.'
        );
    }

    public function me(Request $request): JsonResponse
    {
        $user = $this->authService->me($request->user());

        return ApiResponse::success(
            new UserResource($user),
            'Usuario autenticado.'
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return ApiResponse::success(
            null,
            'Sesión cerrada correctamente.'
        );
    }
}