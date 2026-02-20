<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UserLoginRequest;
use App\Http\Requests\Auth\UserRegistrationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(UserLoginRequest $request) {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = $request->user();
        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
        ];
    }

    public function register(UserRegistrationRequest $request) {
        $validated = $request->safe()->except(['password_confirmation']);

        $user = User::create($validated);
        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
        ];
    }
}
