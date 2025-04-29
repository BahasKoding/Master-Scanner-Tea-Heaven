<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class LogRequests
{
    public function handle($request, Closure $next)
    {
        Log::info('Incoming request', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'input' => $request->all(),
        ]);

        $response = $next($request);

        Log::info('Outgoing response', [
            'status' => $response->status(),
            'content' => $response->getContent(),
        ]);

        return $response;
    }
}