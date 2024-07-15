<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{

    public function ResponseSuccess($data, $metadata = null, $message = "Successful", $status_code = 200, $status = 'success'): JsonResponse
    {
        return response()->json([
            'status'      => $status,
            'status_code' => $status_code,
            'message'     => $message,
            'data'        => $data,
            'metadata'    => $metadata,
        ]);
    }

    public function ResponseError($errors, $metadata = null, $message = "Data Process Error", $status_code = 400, $status = 'error'): JsonResponse
    {
        return response()->json([
            'status'      => $status,
            'status_code' => $status_code,
            'message'     => $message,
            'errors'      => $errors,
            'metadata'    => $metadata,
        ], $status_code);
    }

    public function ResponseInfo($info, $metadata = null, $message = "Notification!", $status_code = 200, $status = 'info'): JsonResponse
    {
        return response()->json([
            'status'      => $status,
            'status_code' => $status_code,
            'message'     => $message,
            'info'        => $info,
            'metadata'    => $metadata,
        ]);
    }
}
