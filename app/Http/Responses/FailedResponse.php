<?php

namespace App\Http\Responses;

use App\Http\Responses\SuccessResponse;

class FailedResponse extends SuccessResponse
{
    public function __construct(
        $status = false,
        $message = "failed",
        $status_code = 400
    ) {
        parent::__construct(
            $data = null,
            $status,
            $message,
            $status_code,
            $headers = [],
            $options = 0
        );
    }
}
