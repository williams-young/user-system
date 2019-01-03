<?php

namespace App\Services;

use App\Api\ReturnCode;
use App\Exceptions\ServiceException;

class BaseService
{

    protected function throwServiceException($message = 'Internal Server Error', $code = ReturnCode::UNKNOWN_ERROR)
    {
        throw new ServiceException($message, $code);
    }

}