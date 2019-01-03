<?php

namespace App\Api\Controllers;

use App\Api\Requests\UserRequest;
use App\Api\ReturnCode;
use App\Exceptions\ServiceException;
use App\Models\Token;
use App\Services\UserService;

class UserController extends BaseController
{
    protected $userService;

    public function __construct(UserService $service)
    {
        $this->userService = $service;
    }

    public function test()
    {
        return $this->responseSuccess($this->user()->detail()->first());
    }

    public function login(UserRequest $request)
    {
        $this->logAccess(__METHOD__, $request->except('password'));
        $credentials = $request->only(['username', 'password']);

        if (! $user = $this->userService->authenticateUser($credentials['username'], $credentials['password'])) {
            $this->logError('登录失败: 用户名或密码错误', __METHOD__, $request->except('password'));
            return $this->responseError(__('app.invalid_username_password'), ReturnCode::INVALID_USERNAME_PASSWORD);
        }

        $token = $this->userService->generateToken($user, Token::TYPE_APP);
        $this->userService->markLoginSuccess($user);
        $this->logService('登录成功', __METHOD__, $request->except('password'), $user->username);
        return $this->responseSuccess($token);
    }

    public function refresh()
    {
        $this->logAccess(__METHOD__, \Request::input(), $this->user()->username);
        try {
            $token = $this->userService->refreshToken($this->user(), Token::TYPE_APP);
            $this->logService('token刷新成功', __METHOD__, \Request::input(), $this->user()->username);
            return $this->responseSuccess($token);
        } catch (\Exception $e) {
            $this->logError('token刷新失败: ' . $e->getMessage(), __METHOD__, \Request::input(), $this->user()->username);
            $extraInfo = $e instanceof ServiceException ?  ': ' . $e->getMessage() : '';
            return $this->responseError(__('app.failed') . $extraInfo);
        }
    }


}