<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function sendResponse($result, $message)
    {
        $response = [
            'status' => Response::HTTP_OK,
            'message' => $message,
            'data' => $result,
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    protected function sendError(
        $error,
        $errorMessage = [],
        $status = Response::HTTP_BAD_REQUEST
    ) {
        $response = [
            'status' => $status,
            'message' => $error,
        ];

        if (!empty($errorMessage)) {
            $response['data'] = $errorMessage;
        }

        return response()->json($response, $status);
    }
}
