<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|min:8|max"16|confirmed' // 'confirmed' looks for password_confirmation
        ]);
    
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => $fields['password'],
            'role' => 'user',     // Default role
            'status' => 'active', // Default status
            'points' => 100       // Starting points for your thesis system
        ]);
    
        $token = $user->createToken('emergency_token')->plainTextToken;
    
        return response([
            'user' => $user,
            'token' => $token
        ], 201);
    }
    public function login(Request $request) 
    {
        $fields = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|max:16'
        ]);

        $user = User::where('email', $fields['email'])->first();

        // DETECTIVE CHECK 1: Does the email even exist?
        if (!$user) {
            return response([
                'message' => 'DETECTIVE REPORT: The email was not found in the database! The seeder probably failed.'
            ], 401);
        }

        // DETECTIVE CHECK 2: Does the password match?
        if (!\Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'DETECTIVE REPORT: Email found, but the password hash is completely wrong!'
            ], 401);
        }

        // 4. Generate a secure Sanctum token
        $token = $user->createToken('emergency_bd_token')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token
        ], 201);
    }
}