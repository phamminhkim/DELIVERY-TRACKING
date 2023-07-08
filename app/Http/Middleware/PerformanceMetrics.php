<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class PerformanceMetrics
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $start = microtime(true);
        $response = $next($request);
        $end = microtime(true);

        $route = $request->route();
        $controller = class_basename(optional($route)->getController());
        $action = optional($route)->getActionMethod();
        $middleware = implode(',', optional($route)->gatherMiddleware());
        $response_time = round($end - $start, 2);
        $memory_usage = memory_get_peak_usage(true) / 1024 / 1024; // convert to MB

        // you might want to persist these data into a database
        Log::channel('performances')->info('Performance Metrics', [
            'route' => $route,
            'controller' => $controller,
            'action' => $action,
            'middleware' => $middleware,
            'response_time' => $response_time,
            'memory_usage' => $memory_usage,
        ]);

        return $response;
    }
}
