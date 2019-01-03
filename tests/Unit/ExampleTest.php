<?php

namespace Tests\Unit;

use App\Services\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /** @var  UserService */
    protected $userService;

    public function setUp()
    {
        parent::setUp();
        $this->userService = $this->app->make('App\Services\UserService');
    }

    public function testAuthenticateUserFail()
    {
        $result = $this->userService->authenticateUser('admin', 'admin');
        $this->assertFalse($result);
    }

    public function testAuthenticateUser()
    {
        $result = $this->userService->authenticateUser('admin', '123456');
        $this->assertArrayHasKey('username', $result);
        $this->assertEquals('admin', $result->username);
    }
}
