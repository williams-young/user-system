<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'app'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'app' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('APP_LOG_LEVEL', 'debug'),
            'days' => 7,
        ],

        'access' => [
            'driver' => 'single',
            'path' => storage_path() . '/logs/' . date('Y-m-d') . '/access.log',
            'level' => 'info',
            'days' => 0,
        ],

        'service' => [
            'driver' => 'single',
            'path' => storage_path() . '/logs/' . date('Y-m-d') . '/service.log',
            'level' => 'info',
            'days' => 0,
        ],

        'error' => [
            'driver' => 'single',
            'path' => storage_path() . '/logs/' . date('Y-m-d') . '/error.log',
            'level' => 'error',
            'days' => 0,
        ],
    ],

];
