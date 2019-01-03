<?php

namespace App\Api;


class ReturnCode
{
    const UNKNOWN_ERROR = 0;
    const SUCCESS = 2000;

    const TOKEN_EXPIRED = 1000;
    const INVALID_TOKEN = 1001;
    const INVALID_PARAMETER = 1002;
    const INSUFFICIENT_PERMISSION = 1003;
    const MODEL_NOT_FOUND = 1004;
    const REPEAT_INSERTION = 1005;
    const SERVICE_DENIED = 1006;

    const INVALID_USERNAME_PASSWORD = 2001;

    const INTERNAL_SERVER_ERROR = 5000;
    const DATABASE_ERROR = 5001;
}