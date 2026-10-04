<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class TokenAuth
{
    /**
     * Handle Sanctum-style Bearer Token Authentication for REST API.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization', '');
        $token = null;

        if (preg_match('/Bearer\s+(.+)/i', $header, $matches)) {
            $token = trim($matches[1]);
        }

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบ Token ยืนยันตัวตน (Authorization header with Bearer token is required)',
            ], 401);
        }

        // Token lookup from Cache
        $userId = Cache::get('sanctum_token_' . $token);

        if (!$userId) {
            // Also allow seeded default tokens for instant testing
            if ($token === 'admin-test-token-2026') {
                $user = User::where('role', 'admin')->first();
            } elseif ($token === 'user-test-token-2026') {
                $user = User::where('role', 'user')->first();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Token ไม่ถูกต้องหรือหมดอายุแล้ว (Invalid or expired token)',
                ], 401);
            }
        } else {
            $user = User::find($userId);
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบบัญชีผู้ใช้งานในระบบ',
            ], 401);
        }

        // Set user into request and Auth guard
        Auth::setUser($user);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}
