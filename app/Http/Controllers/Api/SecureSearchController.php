<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class SecureSearchController extends Controller
{
    /**
     * Get rate limit status for the current IP
     */
    public function getRateLimitStatus(Request $request)
    {
        $key = 'secure-search:' . $request->ip();
        $attempts = RateLimiter::attempts($key);
        $remaining = RateLimiter::remaining($key, 10);
        $availableIn = RateLimiter::availableIn($key);

        return response()->json([
            'success' => true,
            'data' => [
                'attempts'    => $attempts,
                'remaining'   => $remaining,
                'available_in' => $availableIn,
                'limit'       => 30,
                'window'      => 60
            ]
        ]);
    }
}
