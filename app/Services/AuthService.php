<?php

namespace App\Services;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Contracts\User as GoogleUser;

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

    public function register(array $data): array
    {
        $user = User::create([
            'username' => $data['username'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'password' => $data['password'],
            'rol_id' => $this->getComunRoleId(),
        ]);

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

    public function changePassword(User $user, array $data): void
    {
        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['La contraseña actual no es correcta.'],
            ]);
        }

        $user->update([
            'password' => $data['password'],
        ]);

        $user->tokens()->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | Google OAuth
    |--------------------------------------------------------------------------
    */

    public function getGoogleRedirectUrl(): string
    {
        return Socialite::driver('google')
            ->stateless()
            ->redirect()
            ->getTargetUrl();
    }

    public function handleGoogleCallback(): array
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        if (!$googleUser->getEmail() || !($googleUser->getRaw()['email_verified'] ?? false)) {
            throw ValidationException::withMessages([
                'email' => ['Tu cuenta de Google no tiene el correo electrónico verificado.'],
            ]);
        }

        $user = $this->findOrCreateGoogleUser($googleUser);

        $user->load('rol');

        $token = $this->createAccessToken($user);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    private function findOrCreateGoogleUser(GoogleUser $googleUser): User
    {
        $existingUser = User::where('email', $googleUser->getEmail())->first();

        if ($existingUser) {
            $existingUser->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);

            return $existingUser;
        }

        return User::create([
            'username' => $googleUser->getName() ?? $googleUser->getEmail(),
            'lastname' => '',
            'email' => $googleUser->getEmail(),
            'password' => bcrypt(Str::random(32)),
            'rol_id' => $this->getComunRoleId(),
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
        ]);
    }

    private function getComunRoleId(): int
    {
        $rol = Rol::where('slug', 'comun')->first();

        if (!$rol) {
            $rol = Rol::create([
                'slug' => 'comun',
                'name' => 'Comun',
            ]);
        }

        return $rol->id;
    }

    /*
    |--------------------------------------------------------------------------
    | Private Helpers
    |--------------------------------------------------------------------------
    */

    private function findUserByEmail(string $email): User
    {
        $user = User::whereRaw('LOWER(email) = LOWER(?)', [$email])->first();
        
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
            Log::warning('Failed login attempt', [
                'email' => $user->email,
                'user_id' => $user->id,
                'ip' => request()->ip(),
            ]);

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