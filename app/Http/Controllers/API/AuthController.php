<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    /**
     * Register a new user.
     * 
     * @group Authentication
     * 
     * @bodyParam name string required The full name of the user. Example: John Doe
     * @bodyParam email string required The email of the user. Must be unique. Example: john.doe@example.com
     * @bodyParam password string required The password for the user. Must be at least 8 characters. Example: password123
     * @bodyParam password_confirmation string required The password confirmation. Must match the password. Example: password123
     * 
     * @response 200 {
     *   "message": "User registered successfully"
     * }
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    /**
     * Generate an authentication token.
     * 
     * @group Authentication
     * 
     * @bodyParam email string required The email of the user. Example: john.doe@example.com
     * @bodyParam password string required The password of the user. Example: password123
     * 
     * @response 200 {
     *   "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
     * }
     * @response 422 {
     *   "message": "The provided credentials are incorrect.",
     *   "errors": {
     *     "email": ["The provided credentials are incorrect."]
     *   }
     * }
     */
    public function generateToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('my-app-token')->plainTextToken;

        return response()->json(['token' => $token], 200);
    }

    /**
     * Logout and revoke tokens.
     * 
     * @group Authentication
     * 
     * @authenticated
     * 
     * @response 200 {
     *   "message": "You have been successfully logged out."
     * }
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'You have been successfully logged out.'], 200);
    }

    /**
     * Send a password reset link to the user's email.
     * 
     * @group Password Reset
     * 
     * @bodyParam email string required The email of the user. Example: john.doe@example.com
     * 
     * @response 200 {
     *   "message": "Password reset link sent to your email."
     * }
     * @response 500 {
     *   "message": "Failed to send password reset link."
     * }
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Password reset link sent to your email.'], 200);
        } else {
            return response()->json(['message' => 'Failed to send password reset link.'], 500);
        }
    }

    /**
     * Reset the user's password.
     * 
     * @group Password Reset
     * 
     * @bodyParam token string required The password reset token. Example: aBcDeFg12345
     * @bodyParam email string required The email of the user. Example: john.doe@example.com
     * @bodyParam password string required The new password for the user. Must be at least 8 characters. Example: newpassword123
     * @bodyParam password_confirmation string required The password confirmation. Must match the password. Example: newpassword123
     * 
     * @response 200 {
     *   "message": "Password reset successfully."
     * }
     * @response 500 {
     *   "message": "Password reset failed."
     * }
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                $user->tokens()->delete();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password reset successfully.'], 200);
        } else {
            return response()->json(['message' => 'Password reset failed.'], 500);
        }
    }
}
