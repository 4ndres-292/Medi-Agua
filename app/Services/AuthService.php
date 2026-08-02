<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function login(array $credentials): array
    {
        $user = $this->findUserByEmail($credentials['email']);

        $this->validatePassword($user, $credentials['password']);

        $user->load('rol');
        
        $token = $this->createAccessToken($user);
        
        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function me(User $user): User
    {
        return $user->load('rol');
    }

    public function logout(User $user): void
    {
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }
    }

    private function findUserByEmail(string $email): User
    {
        $user = User::where('email', $email)->first();
        
        if(!$user){
            throw ValidationException::withMessages([
                'email' => ['No existe un usuario con esa dirección de correo electrónico.'],
            ]);
        }

        return $user;
    }

    private function validatePassword(User $user, string $password): void
    {
        if (!Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Las credenciales proporcionadas no son correctas.'],
            ]);
        }
    }

    private function createAccessToken(User $user): string
    {
        return $user
            ->createToken('auth-token')
            ->plainTextToken;
    }
}