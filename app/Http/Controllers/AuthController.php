<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $input = $request->input('username') ?? $request->input('email');
        $password = $request->input('password');

        $fieldType = filter_var($input, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [$fieldType => $input, 'password' => $password];

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to login. Wrong username or password',
            ], 401);
        }

        $user = User::with(["walas", "piket", "mapel"])
            ->where($fieldType, $input)
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged in.',
            'data' => $user,
            'access_token' => $token
        ]);
    }

    public function refreshToken(Request $request)
    {
        try {
            $newToken = auth('api')->refresh();
            auth('api')->invalidate(true);
            
            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'access_token' => $newToken
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh token',
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        try {
            auth('api')->logout();
            auth('api')->invalidate(true);

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to logout.'
            ], 500);
        }
    }
}

