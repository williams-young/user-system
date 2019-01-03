<?php

namespace App\Providers;

use App\Api\ReturnCode;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        app(\Dingo\Api\Exception\Handler::class)->register(function (\Exception $exception) {
            if ($exception instanceof UnauthorizedHttpException) {
                return response()->json(['status_code' => ReturnCode::INVALID_TOKEN, 'message' => __('app.token_invalid')], 401);
            } elseif ($exception instanceof QueryException) {
                return response()->json(['status_code' => ReturnCode::DATABASE_ERROR, 'message' => __('app.query_exception')], 500);
            } elseif ($exception instanceof ModelNotFoundException) {
                return response()->json(['status_code' => ReturnCode::MODEL_NOT_FOUND, 'message' => __('app.not_found')], 404);
            } elseif ($exception instanceof TokenExpiredException) {
                return response()->json(['status_code' => ReturnCode::TOKEN_EXPIRED, 'message' => __('app.token_expired')], 401);
            }
        });
    }
}
