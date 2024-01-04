<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class SuccessResponse extends JsonResponse
{
    public function __construct(
        $data = null,
        $status = true,
        $message = null,
        $status_code = 200
    ) {
        $responseData = [
            "status" => $status,
            "message" => $message ?: "success",
        ];

        if ($data !== null) {
            $responseData = array_merge($responseData, $data);
        }

        parent::__construct(
            $responseData,
            $status_code,
            $headers = [],
            $options = 0
        );
    }
}
