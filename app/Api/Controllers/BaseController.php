<?php

namespace App\Api\Controllers;

use App\Api\ReturnCode;
use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;
use Validator;
use Response;


class BaseController extends Controller
{
    protected $module = 'Base';

    use Helpers;

    protected function logAccess($method, $data, $user = null)
    {
        $user = $user ? $user . ' ' : '';
        \Log::channel('access')->info('[' . get_client_ip() . '] ' . $user . 'access ' . $method, $data);
    }

    protected function logService($message, $method, $data, $user = null)
    {
        $user = $user ? $user . ' ' : '';
        \Log::channel('service')->info('[' . get_client_ip() . '] ' . $user . $message . ': ' . $method, $data);
    }

    protected function logError($message, $method, $data, $user = null)
    {
        $user = $user ? $user . ' ' : '';
        \Log::channel('error')->error('[' . get_client_ip() . '] ' . $user . $message . ': ' . $method, $data);
    }

    protected function responseSuccess($data = [], $message = 'success')
    {
        return Response::json([
            'status_code' => ReturnCode::SUCCESS,
            'message' => $message,
            'data' => $data,
        ]);
    }

    protected function responseError($message, $code = ReturnCode::UNKNOWN_ERROR)
    {
        return Response::json([
            'status_code' => $code,
            'message' => $message,
        ]);
    }

    protected function validateInput($input, $rules, $messages = [])
    {
        $validate = Validator::make($input, $rules, $messages);
        return $validate->passes() ? : $validate->messages()->first();
    }


}