<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-KEY');

        if (!$apiKey || $apiKey !== config('services.api.key')) {
            return response()->json([
                'message' => 'Invalid or missing API key'
            ], 401);
        }

        return $next($request);
    }
}
