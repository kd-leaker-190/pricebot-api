<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use App\Http\Resources\UserResource;
use App\Mail\EmailVerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class EmailVerificationController extends ApiController
{
    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $user = Auth::user();

        if ($request->code != $user->email_verification_code) {
            return $this->errorResponse('Wrong verification code', 422);
        }

        if (!$user->email_verification_code_expires_at || now()->greaterThan($user->email_verification_code_expires_at)) {
            return $this->errorResponse('Verification code expired or not exists', 422);
        }

        $user->update([
            'email_verified_at' => now(),
            'email_verification_code' => null,
            'email_verification_code_expires_at' => null,
        ]);

        return $this->successResponse(new UserResource($user), 200, 'Email verified successfully');
    }
    public function resendVerificationCode()
    {
        $user = Auth::user();

        if ($user->email_verified_at) {
            return $this->errorResponse('Email already verified', 422);
        }

        $code = random_int(100000, 999999);

        $user->update([
            'email_verification_code' => $code,
            'email_verification_code_expires_at' => now()->addMinutes(15),
        ]);

        Mail::to($user->email)->send(new EmailVerificationCode($code));

        return $this->successResponse(new UserResource($user), 200, 'Verification code resended');
    }
}
