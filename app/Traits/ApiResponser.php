<?php

namespace App\Traits;

trait ApiResponser
{
    public function successResponse($data, $status, $message = '')
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $status);
    }
    public function errorResponse($message, $status)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => '',
        ], $status);
    }
}
