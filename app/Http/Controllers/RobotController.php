<?php

namespace App\Http\Controllers;

use App\Http\Resources\RobotResource;
use App\Http\Resources\UserResource;
use App\Models\Robot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RobotController extends ApiController
{
    public function launch(Request $request)
    {
        $valdiator = Validator::make(request()->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'domain' => 'required|string|unique:robots,domain',
            'currency' => 'required|string|in:USD,EUR,JPY,GBD',
            'shop_name' => 'required|string|unique:robots,shop_name',
            'wp_consumer_key' => 'required|string',
            'wp_consumer_secret' => 'required|string',
        ]);

        if ($valdiator->fails()) {
            return $this->errorResponse($valdiator->messages(), 422);
        }

        $robot = Robot::create([
            'user_id' => $request->user_id,
            'domain' => $request->domain,
            'currency' => $request->currency,
            'shop_name' => $request->shop_name,
            'wp_consumer_key' => $request->wp_consumer_key,
            'wp_consumer_secret' => $request->wp_consumer_secret,
        ]);

        $robot->load('user');

        return $this->successResponse(new RobotResource($robot), 201, 'Robot configured successfully');
    }
}
