<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class LogApiCall
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
        $response = $next($request);

        if ($response->getStatusCode() != 200) {
            Log::channel('api-call')->error('Api call failed', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'params' => $request->all(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'response' => $response->getContent(),
            ]);
        }

        return $response;
    }
}
