<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    // User Registration
    public function register(Request $request)
    {

        
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'email' => 'required|string|email|unique:users',
        //     'password' => 'required|string|min:8|confirmed',
        // ]);

        // dd($request);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    //generate token 
    public function generateToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('my-app-token')->plainTextToken;

        return response()->json(['token' => $token], 200);
    }

    // Method to handle user logout and token revocation
    public function logout(Request $request)
    {
		// Revoke all tokens...
		$request->user()->tokens()->delete();

		// // Revoke the current token
		$request->user()->currentAccessToken()->delete();

		return response()->json(['message' => 'You have been successfully logged out.'], 200);
    }
}
