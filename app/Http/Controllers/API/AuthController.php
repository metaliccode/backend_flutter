<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        // sql

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            // 'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('MyTokenApp')->plainTextToken;
        // json response
        $response = [
            'message' => 'User berhasil dibuat',
            'user' => $user,
            'token' => $token,
        ];
        return response()->json($response, 201); // 201 = created
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:6|',
        ]);

        // logika login jika user & password benar
        $user = User::where('email', $fields['email'])->first();
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid creadentials',
            ], 404);
        }

        $token = $user->createToken('MyTokenApp')->plainTextToken;
        $response = [
            'message' => 'User berhasil login',
            'user' => $user,
            'token' => $token,
        ];
        return response()->json($response, 200); // ok
    }

    public function logout()
    {
        auth()->user()->tokens->each(
            function ($token) {
                $token->delete();
            }
        );

        return response()->json([
            'message' => 'User berhasil logout',
        ], 200); // ok
    }

    public function user()
    {
        return response()->json([
            'user' => auth()->user(),
        ]);
    }
}
