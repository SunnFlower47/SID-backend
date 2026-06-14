<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LowercaseHost
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->headers->get('HOST');
        if ($host) {
            $request->headers->set('HOST', strtolower($host));
        }

        // Update the server parameters for HTTP_HOST and SERVER_NAME
        $request->server->set('HTTP_HOST', strtolower($request->server->get('HTTP_HOST', '')));
        $request->server->set('SERVER_NAME', strtolower($request->server->get('SERVER_NAME', '')));

        return $next($request);
    }
}
