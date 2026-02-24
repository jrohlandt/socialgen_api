<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UserLoginRequest;
use App\Http\Requests\Auth\UserRegistrationRequest;
use App\Models\User;
use Illuminate\Contracts\Auth\Factory as Auth;

class AuthController extends Controller
{
    public function __construct(protected Auth $auth)
    {
    }

    public function login(UserLoginRequest $request)
    {
        $credentials = $request->validated();

        if (! $this->auth->guard()->attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = $request->user();
        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
        ];
    }

    public function register(UserRegistrationRequest $request)
    {
        $validated = $request->safe()->except(['password_confirmation']);

        $user = User::create($validated);
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
            'message' => 'Restration success!',
        ], 201);
    }
}
