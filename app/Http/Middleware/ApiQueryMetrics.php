<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;

class ApiQueryMetrics
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (env('APP_DEBUG') == true) {
            \DB::enableQueryLog();
        }

        $response = $next($request);

        if (env('APP_DEBUG') == true && $response instanceof JsonResponse) {
            $logs = collect(\DB::getQueryLog());
            $count = $logs->count();
            $time = $logs->sum(function ($item) {
                return $item['time'];
            });

            $different = $logs->unique('query')->count();

            $data = $response->getData();
            if (!is_object($data)) {
                $data = new \stdClass();
                $data->data = $response->getData();
            }
            $data->query_num = $count;
            $data->query_time = $time > 1000 ? sprintf('%.2f s', $time / 1000) : $time . ' ms';
            $data->different_query = $different;
            $data->queries = $logs->all();
            $response->setData($data);
        }

        return $response;
    }
}
