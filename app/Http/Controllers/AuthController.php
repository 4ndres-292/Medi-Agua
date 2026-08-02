<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\AuthService;
use App\Http\Resources\LoginResource;
use App\Http\Resources\UserResource;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }


    public function login(LoginRequest $request): JsonResponse
    {
        $data = $this->authService->login($request->validated());

        return ApiResponse::success(
            new LoginResource($data),
            'Inicio de sesión exitoso.'
        );
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $this->authService->register($request->validated());

        return ApiResponse::success(
            new LoginResource($data),
            'Usuario registrado exitosamente.',
            201
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

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $this->authService->changePassword($request->user(), $request->validated());

        return ApiResponse::success(
            null,
            'Contraseña actualizada correctamente.'
        );
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        $message = $status === Password::RESET_LINK_SENT
            ? 'Si el correo existe, se enviará un enlace para restablecer la contraseña.'
            : 'Si el correo existe, se enviará un enlace para restablecer la contraseña.';

        return ApiResponse::success(null, $message);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => $password,
                ])->save();

                $user->tokens()->delete();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? ApiResponse::success(null, 'Contraseña restablecida correctamente.')
            : ApiResponse::error('No se pudo restablecer la contraseña. El token puede ser inválido o haber expirado.', null, 422);
    }

    /*
    |--------------------------------------------------------------------------
    | Google OAuth
    |--------------------------------------------------------------------------
    */

    public function googleRedirect(): RedirectResponse
    {
        $url = $this->authService->getGoogleRedirectUrl();

        return redirect($url);
    }

    public function googleCallback(): JsonResponse
    {
        try {
            $data = $this->authService->handleGoogleCallback();

            return ApiResponse::success(
                new LoginResource($data),
                'Inicio de sesión con Google exitoso.'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::error(
                $e->getMessage(),
                $e->errors(),
                422
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Error al autenticar con Google. Intente nuevamente.',
                null,
                500
            );
        }
    }
}