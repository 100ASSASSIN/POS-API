<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
   public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Check user exists and is active
    $user = User::where('email', $credentials['email'])->first();

    if (! $user) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    if ((int) $user->status === 0) {
        return response()->json([
            'message' => 'Your account is disabled. Please contact admin.'
        ], 403);
    }

    // Attempt login
    if (! $token = auth('api')->attempt($credentials)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    return response()
        ->json([
            'user' => auth('api')->user(),
            'token' => $token,
            'token_type' => 'bearer',
        ])
        ->cookie(
            'access_token',
            $token,
            60,
            '/',
            null,
            false,
            true,
            false,
            'Lax'
        );
}


    public function me()
    {
        return response()->json(auth('api')->user());
    }

    public function logout()
    {
        auth('api')->logout();

        return response()
            ->json(['message' => 'Logged out'])
            ->cookie('access_token', '', -1);
    }
}
