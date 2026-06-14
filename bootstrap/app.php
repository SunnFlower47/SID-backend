<?php

    use Illuminate\Foundation\Application;
    use Illuminate\Foundation\Configuration\Exceptions;
    use Illuminate\Foundation\Configuration\Middleware;
    use App\Http\Middleware\PrivateApiMiddleware;
    use App\Http\Middleware\RecaptchaForgotPasswordMiddleware;
    use App\Http\Middleware\RecaptchaResetPasswordMiddleware;
    use App\Http\Middleware\CaptureActivityLogData;
    use App\Http\Middleware\CsrfApiMiddleware;
    use App\Http\Middleware\CspNonceMiddleware;

    return Application::configure(basePath: dirname(__DIR__))
        ->withRouting(
            api: __DIR__.'/../routes/api.php',
            commands: __DIR__.'/../routes/console.php',
            health: '/up',
            then: function () {
                // Landlord routes
                \Illuminate\Support\Facades\Route::middleware('web')
                    ->domain(env('LANDLORD_DOMAIN', 'diskominfo.sistem-desa-cibatu.test'))
                    ->group(base_path('routes/landlord.php'));

                // Tenant/Admin Panel routes
                \Illuminate\Support\Facades\Route::middleware('web')
                    ->domain(env('ADMIN_DOMAIN', 'admin.sistem-desa-cibatu.test'))
                    ->group(base_path('routes/web.php'));
            }
        )
        ->withMiddleware(function (Middleware $middleware): void {
            // Trust all proxies (Required for local Proxy and Cloudflare/Nginx)
            $middleware->trustProxies(at: '*');
            
            // Register custom middleware
            $middleware->alias([
                'private.api' => PrivateApiMiddleware::class,
                'captcha' => \App\Http\Middleware\VerifyRecaptcha::class,
                'recaptcha' => \App\Http\Middleware\VerifyRecaptcha::class,
                'recaptcha.forgot' => RecaptchaForgotPasswordMiddleware::class,
                'recaptcha.reset' => RecaptchaResetPasswordMiddleware::class,
                'capture.activity' => CaptureActivityLogData::class,
                'csrf.api' => CsrfApiMiddleware::class,
                'csp.nonce' => CspNonceMiddleware::class,
                'tenant.auth' => \App\Http\Middleware\InitializeTenancyByLoggedInUser::class,
                'tenant.api' => \App\Http\Middleware\InitializeTenancyForApi::class,
            ]);

            // Set middleware priority
            $middleware->priority([
                \Illuminate\Cookie\Middleware\EncryptCookies::class,
                \Illuminate\Session\Middleware\StartSession::class,
                \Illuminate\View\Middleware\ShareErrorsFromSession::class,
                \App\Http\Middleware\InitializeTenancyByLoggedInUser::class,
                \Illuminate\Auth\Middleware\Authenticate::class,
                \Illuminate\Routing\Middleware\ThrottleRequests::class,
                \Illuminate\Routing\Middleware\ThrottleRequestsWithRedis::class,
                \Illuminate\Session\Middleware\AuthenticateSession::class,
                \Illuminate\Routing\Middleware\SubstituteBindings::class,
                \Illuminate\Auth\Middleware\Authorize::class,
            ]);

            // Add activity log capture middleware to web group
            $middleware->web(append: [
                CaptureActivityLogData::class,
                CspNonceMiddleware::class,
                \App\Http\Middleware\HandleInertiaRequests::class,
                \App\Http\Middleware\PreserveIndexFiltersMiddleware::class,
                \App\Http\Middleware\CheckTenantActive::class,
            ]);


            // API middleware group - minimal untuk private API
            $middleware->group('api', [
                // Rate limiting diatur per route, bukan di group
            ]);

            // Custom redirect for unauthenticated users based on domain
            $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
                if ($request->getHost() === env('LANDLORD_DOMAIN', 'diskominfo.sistem-desa-cibatu.test')) {
                    return route('landlord.login');
                }
                
                return route('login');
            });
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
