<?php

namespace App\Api\Requests;

use App\Api\ReturnCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class Request extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        $errors = $this->formatErrors($validator);

        throw new HttpResponseException(response()->json(['status_code' => ReturnCode::INVALID_PARAMETER, 'message' => $errors[0]]));
    }

    protected function formatErrors(Validator $validator)
    {
        return $validator->errors()->all();
    }

}
