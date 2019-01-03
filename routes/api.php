<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Api\Controllers', 'middleware' => 'api'], function ($api) {

        // routes that should be authorized
        $api->group(['middleware' => 'api.auth'], function ($api) {

            // user
            $api->get('test', 'UserController@test');
            $api->get('refresh', ['as' => 'tokens.refresh', 'uses' => 'UserController@refresh']);

        });

        $api->get('login', 'UserController@login');

    });
});