<?php

namespace Tests\Feature;

use App\Api\ReturnCode;
use App\Models\User;
use Tests\TestCase;

class ExampleTest extends TestCase
{

    public function testLogin()
    {
        // error parameter
        $this->get('/api/login')
            ->assertJsonFragment(['status_code' => ReturnCode::INVALID_PARAMETER]);

        // invalid username or password
        $this->get('/api/login?username=admin&password=123klsadhf')
            ->assertJsonFragment(['status_code' => ReturnCode::INVALID_USERNAME_PASSWORD]);

        // login
        $this->get('/api/login?username=admin&password=123456')
            ->assertJsonStructure([
                'status_code',
                'data' => ['token', 'expired_time', 'refresh_token']
            ])
            ->assertJsonFragment(['status_code' => ReturnCode::SUCCESS]);
    }

    public function testTest()
    {
        $uri = '/api/test';

        $this->get($uri, $this->headers())->assertStatus(401);
        $this->get($uri, $this->headers(User::first()))->assertStatus(200);
    }


    protected function headers($user = null)
    {
        $headers = ['Accept' => 'application/json'];

        if (!is_null($user)) {
            $token = \JWTAuth::fromUser($user);
            \JWTAuth::setToken($token);
            $headers['Authorization'] = 'Bearer '.$token;
        }

        return $headers;
    }
}
