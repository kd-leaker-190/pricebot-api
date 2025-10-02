<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use App\Http\Resources\UserResource;
use App\Mail\EmailVerificationCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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

        $token = $user->createToken('auth_token')->plainTextToken;

        $code = random_int(100000, 999999);
        $user->update([
            'email_verification_code' => $code,
            'email_verification_code_expires_at' => now()->addMinutes(15),
        ]);

        Mail::to($user->email)->send(new EmailVerificationCode($code));

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
        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Your credentials does not match ours', 401);
        }

        if (!$user->email_verified_at) {
            return $this->errorResponse('Email not verified', 403);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $token,
        ], 200, 'Logged in successfully');
    }
    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->tokens()->delete();
        return $this->successResponse(new UserResource(Auth::user()), 200, 'Logged out successfully');
    }
    public function me()
    {
        $user = Auth::user();
        $user->load('robot');
        return $this->successResponse(new UserResource(Auth::user()), 200, '');
    }
}
