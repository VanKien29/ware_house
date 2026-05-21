<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Đăng nhập thất bại, vui lòng kiểm tra lại email và mật khẩu',
            ], 401);
        }

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken,
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $token = $user?->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        } else {
            $user?->tokens()->delete();
        }

        return response()->json(['message' => 'Đăng xuất thành công']);
    }
}
