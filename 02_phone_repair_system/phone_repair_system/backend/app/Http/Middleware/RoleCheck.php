<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleCheck
{
    /**
     * Handle an incoming request for role-based authorization.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'กรุณาเข้าสู่ระบบก่อนใช้งาน (Unauthenticated)',
                ], 401);
            }
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบก่อนใช้งาน');
        }

        if (!in_array($user->role, $roles)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'คุณไม่มีสิทธิ์เข้าถึงส่วนนี้ (Forbidden - ' . implode(',', $roles) . ' only)',
                ], 403);
            }
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงส่วนนี้ (สำหรับ ' . implode('/', $roles) . ' เท่านั้น)');
        }

        return $next($request);
    }
}
