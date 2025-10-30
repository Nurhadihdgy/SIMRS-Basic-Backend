<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\SimrsUser;

class AuthController extends Controller
{
    // LOGIN
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = SimrsUser::where('user_username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->user_password)) {
            return response()->json(['message' => 'username or password are incorrect'], 401);
        }

        $token = $user->createToken('simrs_token')->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'token' => $token,
            'user' => [
                'user_id' => $user->user_id,
                'user_full_name' => $user->user_full_name,
                'user_username' => $user->user_username,
                'created_at' => $user->created_at,
            ]
        ], 200);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logout success'], 200);
        }

        return response()->json(['message' => 'Unauthenticated.'], 401);
    }
}
