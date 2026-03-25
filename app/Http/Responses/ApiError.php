<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiError
{
    public static function respond(
        string $code,
        string $message,
        int $status = 422,
        array $errors = []
    ): JsonResponse {
        return response()->json([
            'error'   => $code,
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }
}
