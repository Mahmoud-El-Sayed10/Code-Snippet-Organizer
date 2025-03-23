<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = [
            "email" => $request["email"], 
            "password" => $request["password"]
        ];

        if (!$token = Auth::attempt($credentials)) {
            return response()->json([
                "success" => false,
                "error" => "Unauthorized"
            ], 401);
        }

        $user = Auth::user();
        $user->token = $token;
        
        return response()->json([
            "success" => true,
            "user" => $user
        ]);
    }

    public function register(Request $request)
    {
        // Check if email already exists
        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                "success" => false,
                "error" => "Email already taken"
            ], 400);
        }
        
        // Check required fields
        if (!$request->name || !$request->email || !$request->password) {
            return response()->json([
                "success" => false,
                "error" => "Name, email and password are required"
            ], 400); 
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            "success" => true,
            "message" => 'User successfully registered',
            "user" => $user
        ], 201);
    }

    public function logout()
    {
        Auth::logout();

        return response()->json([
            "success" => true,
            "message" => 'User successfully signed out'
        ]);
    }

    public function refresh()
    {
        $token = Auth::refresh();
        $user = Auth::user();
        $user->token = $token;
        
        return response()->json([
            "success" => true,
            "user" => $user
        ]);
    }

    public function userProfile()
    {
        return response()->json([
            "success" => true,
            "user" => Auth::user()
        ]);
    }

    public function editProfile(Request $request)
    {
        $userId = Auth::id();
        $user = User::find($userId);

        $user->name = $request->name ? $request->name : $user->name;
        $user->email = $request->email ? $request->email : $user->email;
        
        $user->save();

        return response()->json([
            "success" => true,
            "user" => $user
        ]);
    }

}