<?php

namespace App\Http\Controllers\Api;

use App\Attribute;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    protected function message($code, $message, $data = [])
    {
        $responseData = [
            'message' => $message,
        ];
        if ($data) {
            $responseData['data'] = $data;
        }
        return response()->json($responseData, $code);
    }
}
