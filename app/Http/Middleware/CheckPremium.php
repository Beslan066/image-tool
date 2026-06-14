<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPremium
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Для неавторизованных пользователей - только базовые функции
        if (!$user) {
            if ($request->ajax() && $request->has('premium_only')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Функция доступна только в Premium подписке',
                    'requires_premium' => true
                ], 403);
            }
            return redirect()->route('converter');
        }

        // Для авторизованных - проверяем премиум
        if ($request->has('premium_only') && !$user->isPremiumActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Функция доступна только в Premium подписке',
                'requires_premium' => true
            ], 403);
        }

        return $next($request);
    }
}
