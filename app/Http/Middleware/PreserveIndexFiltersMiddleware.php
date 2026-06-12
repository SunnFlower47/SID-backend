<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class PreserveIndexFiltersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. If this is a GET request to an index route, save its full URL
        if ($request->isMethod('GET') && $request->route()) {
            $routeName = $request->route()->getName();
            
            // Usahakan untuk melacak rute .index atau show yang bertindak sebagai index (seperti administrasi.buku.show)
            if ($routeName && (Str::endsWith($routeName, '.index') || Str::endsWith($routeName, '.show'))) {
                // Save the full URL including query params (e.g. ?search=budi&page=2)
                session(['index_url_' . $routeName => $request->fullUrl()]);
            }
        }

        // 2. Process the request
        $response = $next($request);

        // 3. If the response is a redirect to an index route, append the saved query params
        if ($response instanceof RedirectResponse && $request->isMethodSafe() === false) {
            $targetUrl = rtrim($response->getTargetUrl(), '/');
            
            // Check if the target URL matches the base URL of any saved index URL
            foreach (session()->all() as $key => $savedUrl) {
                if (Str::startsWith($key, 'index_url_') && is_string($savedUrl)) {
                    // Remove query string from saved URL to get the base
                    $savedBaseUrl = rtrim(explode('?', $savedUrl)[0], '/');
                    
                    // If the redirect target matches the base URL, replace it with the full saved URL
                    if ($targetUrl === $savedBaseUrl && $savedBaseUrl !== $savedUrl) {
                        $response->setTargetUrl($savedUrl);
                        break;
                    }
                }
            }
        }

        return $response;
    }
}
