<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only(['logout', 'user']);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($token = auth()->attempt($credentials)) {
            return ['access_token' => $token];
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function register(Request $request)
    {
        $attributes = $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed'
        ]);

        $attributes['password'] = Hash::make($attributes['password']);
        $user = User::create($attributes);

        $token = auth()->login($user);

        return response()->json([
            'access_token' => $token
        ]);
    }

    public function logout()
    {
        auth()->logout();
    }

    public function refresh()
    {
        try {
            return ['access_token' => auth()->refresh()];
        } catch (JWTException $exception) {
            throw new AuthenticationException('Unauthenticated.');
        }
    }

    public function user()
    {
        return auth()->user();
    }
}
