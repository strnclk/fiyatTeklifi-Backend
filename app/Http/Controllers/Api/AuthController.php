<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register a new user and return a JWT token
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = auth('api')->login($user);

        return $this->respondWithToken($token);
    }

    /**
     * Get a JWT token via given credentials
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            if (!$token = auth('api')->attempt($credentials)) {
                return response()->json([
                    'message' => 'Unauthorized',
                    'errors' => [
                        'email' => ['E-posta veya şifre hatalı']
                    ]
                ], 401);
            }

            return $this->respondWithToken($token);
        } catch (\Throwable $e) {
            \Log::error('Login error: '.$e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            $message = config('app.debug') ? $e->getMessage() : 'Server error';
            return response()->json([
                'message' => 'Login failed',
                'error' => $message,
            ], 500);
        }
    }

    /**
     * Get the authenticated User
     */
    public function me()
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Log the user out (Invalidate the token)
     */
    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Başarıyla çıkış yapıldı']);
    }

    /**
     * Get the token array structure
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->user()
        ]);
    }
}
