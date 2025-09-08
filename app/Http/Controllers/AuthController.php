<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\EmailVerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends ApiController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $code = random_int(100000, 999999);
        $user->email_verification_code = $code;
        $user->save();

        $user->notify(new EmailVerificationCode($code));

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $token,
        ], 201, 'Registered successfully');
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $user = User::where('email', $request->email)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Your credentials does not match ours', 401);
        }

        if (! $user->email_verified_at) {
            return $this->errorResponse('Email not verified', 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $token,
        ], 200, 'Logged in successfully');
    }
    public function logout(Request $request)
    {
        Auth::user()->tokens()->delete();
        return $this->successResponse(new UserResource(Auth::user()), 200, 'Logged out successfully');
    }
    public function me()
    {
        return $this->successResponse(new UserResource(Auth::user()), 200, 'User Details');
    }
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->email_verification_code !== $request->code) {
            return response()->json(['message' => 'Invalid code'], 422);
        }

        $user->email_verified_at = now();
        $user->email_verification_code = null;
        $user->save();

        return $this->successResponse(new UserResource($user), 200, 'Email verified successfully.');
    }
}
