<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Support\ApiResponse;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string $roles)
    {
        $user = $request->user();

        if (!$user) {
            return ApiResponse::error('No autenticado.', null, 401);
        }

        $allowedRoles = array_map('trim', explode(',', $roles));

        if (!in_array($user->role_id, $this->getRoleIds($allowedRoles))) {
            return ApiResponse::error('No tiene permisos para realizar esta acción.', null, 403);
        }

        return $next($request);
    }

    private function getRoleIds(array $roleNames): array
    {
        $roleMap = [
            'admin' => 1,
            'operator' => 2,
            'cashier' => 3,
            'viewer' => 4,
        ];

        return array_map(fn($name) => $roleMap[$name] ?? 0, $roleNames);
    }
}
