<?php

    use Illuminate\Foundation\Application;
    use Illuminate\Foundation\Configuration\Exceptions;
    use Illuminate\Foundation\Configuration\Middleware;
    use App\Http\Middleware\PrivateApiMiddleware;
    use App\Http\Middleware\ApiKeyMiddleware;
    use App\Http\Middleware\SignatureValidationMiddleware;
    use App\Http\Middleware\RecaptchaMiddleware;
    use App\Http\Middleware\RecaptchaForgotPasswordMiddleware;
    use App\Http\Middleware\RecaptchaResetPasswordMiddleware;
    use App\Http\Middleware\CaptureActivityLogData;
    use App\Http\Middleware\CsrfApiMiddleware;
    use App\Http\Middleware\CspNonceMiddleware;
    use App\Http\Middleware\AdminSecurityMiddleware;

    return Application::configure(basePath: dirname(__DIR__))
        ->withRouting(
            web: __DIR__.'/../routes/web.php',
            api: __DIR__.'/../routes/api.php',
            commands: __DIR__.'/../routes/console.php',
            health: '/up',
        )
        ->withMiddleware(function (Middleware $middleware): void {
            // Trust all proxies (Required for local Proxy and Cloudflare/Nginx)
            $middleware->trustProxies(at: '*');
            
            // Register custom middleware
            $middleware->alias([
                'private.api' => PrivateApiMiddleware::class,
                'api.key' => ApiKeyMiddleware::class,
                'signature.validation' => SignatureValidationMiddleware::class,
                'captcha' => \App\Http\Middleware\VerifyRecaptcha::class,
                'recaptcha' => \App\Http\Middleware\VerifyRecaptcha::class,
                'recaptcha.forgot' => RecaptchaForgotPasswordMiddleware::class,
                'recaptcha.reset' => RecaptchaResetPasswordMiddleware::class,
                'capture.activity' => CaptureActivityLogData::class,
                'csrf.api' => CsrfApiMiddleware::class,
                'csp.nonce' => CspNonceMiddleware::class,
                'admin.security' => AdminSecurityMiddleware::class,
            ]);

            // Add activity log capture middleware to web group
            $middleware->web(append: [
                CaptureActivityLogData::class,
                CspNonceMiddleware::class,
                \App\Http\Middleware\HandleInertiaRequests::class,
            ]);


            // API middleware group - minimal untuk private API
            $middleware->group('api', [
                // Rate limiting diatur per route, bukan di group
            ]);
        })
        ->withExceptions(function (Exceptions $exceptions): void {
            $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response, \Throwable $exception, \Illuminate\Http\Request $request) {
                if (!app()->environment(['local', 'testing']) || in_array($response->getStatusCode(), [401, 403, 404, 419, 429, 500, 503])) {
                    // Jika request meminta JSON murni (API atau request non-Inertia), biarkan kembalikan default
                    if ($request->wantsJson() && !$request->header('X-Inertia')) {
                        return $response;
                    }
                    
                    // Untuk 403, 404 dll render via Inertia
                    if (in_array($response->getStatusCode(), [401, 403, 404, 419, 429, 500, 503])) {
                        return \Inertia\Inertia::render('Errors/Error', [
                            'status' => $response->getStatusCode()
                        ])->toResponse($request)->setStatusCode($response->getStatusCode());
                    }
                }
                return $response;
            });
        })->create();
