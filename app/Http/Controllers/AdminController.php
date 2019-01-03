<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AdminController extends BaseController
{
    protected $userService;

    public function __construct(UserService $service)
    {
        $this->userService = $service;
    }

    public function dashboard()
    {
        return \Response::make('dashboard');
    }

    public function login()
    {
        if ($this->guard()->check()) {
            return redirect($this->redirectPath());
        }

        return view('admin.login');
    }

    public function postLogin(Request $request)
    {
        $credentials = $request->only([$this->username(), 'password']);

        $validator = \Validator::make($credentials, [
            $this->username()   => 'required',
            'password'          => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        if ($this->guard()->attempt($credentials)) {
            return $this->sendLoginResponse($request);
        }

        return back()->withInput()->withErrors([
            $this->username() => trans('auth.failed'),
        ]);
    }

    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();
        $this->userService->markLoginSuccess($this->guard()->user());

        return redirect()->intended($this->redirectPath());
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect(url('admin/'));
    }


    protected function username()
    {
        return 'username';
    }

    protected function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            dd($this->redirectTo());
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : url('admin');
    }

    protected function guard()
    {
        return Auth::guard('admin');
    }

}
