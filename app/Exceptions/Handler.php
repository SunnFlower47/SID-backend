<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;
use Log;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Log all exceptions for debugging
            Log::error('Exception occurred', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        });

        // Handle PostTooLargeException (413)
        $this->renderable(function (PostTooLargeException $e, $request) {
            return $this->handleHttpException($request, 413, 'Ukuran file terlalu besar! Maksimal 5MB.');
        });

        // Handle 404 Not Found
        $this->renderable(function (NotFoundHttpException $e, $request) {
            return $this->handleHttpException($request, 404, 'Halaman tidak ditemukan.');
        });

        // Handle 403 Forbidden
        $this->renderable(function (AuthorizationException $e, $request) {
            return $this->handleHttpException($request, 403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        });

        // Handle 401 Unauthorized
        $this->renderable(function (AuthenticationException $e, $request) {
            return $this->handleHttpException($request, 401, 'Anda harus login terlebih dahulu.');
        });

        // Handle 405 Method Not Allowed
        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            return $this->handleHttpException($request, 405, 'Method tidak diizinkan untuk endpoint ini.');
        });

        // Handle 429 Too Many Requests
        $this->renderable(function (TooManyRequestsHttpException $e, $request) {
            return $this->handleHttpException($request, 429, 'Terlalu banyak permintaan. Silakan coba lagi nanti.');
        });

        // Handle Model Not Found
        $this->renderable(function (ModelNotFoundException $e, $request) {
            return $this->handleHttpException($request, 404, 'Data tidak ditemukan.');
        });

        // Handle CSRF Token Mismatch
        $this->renderable(function (TokenMismatchException $e, $request) {
            return $this->handleHttpException($request, 419, 'Sesi telah berakhir. Silakan refresh halaman dan coba lagi.');
        });

        // Handle Validation Exception
        $this->renderable(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors(),
                    'error' => 'VALIDATION_FAILED'
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Terdapat kesalahan dalam data yang dimasukkan.');
        });

        // Handle General HTTP Exceptions
        $this->renderable(function (HttpException $e, $request) {
            return $this->handleHttpException($request, $e->getStatusCode(), $e->getMessage());
        });

        // Handle General Exceptions (500)
        $this->renderable(function (Throwable $e, $request) {
            return $this->handleHttpException($request, 500, 'Terjadi kesalahan server. Silakan coba lagi nanti.');
        });
    }

    /**
     * Handle HTTP exceptions with consistent response format
     */
    private function handleHttpException($request, $statusCode, $message)
    {
        // For AJAX/API requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'error' => $this->getErrorCode($statusCode),
                'status_code' => $statusCode
            ], $statusCode);
        }

        // For web requests, show appropriate error page
        switch ($statusCode) {
            case 401:
                return redirect()->route('login')->with('error', $message);
            case 403:
                return response()->view('errors.403', ['message' => $message], 403);
            case 404:
                return response()->view('errors.404', ['message' => $message], 404);
            case 413:
                return redirect()->back()
                    ->withErrors(['gambar' => $message])
                    ->withInput()
                    ->with('error', $message);
            case 419:
                return redirect()->back()
                    ->with('error', $message);
            case 429:
                return response()->view('errors.429', ['message' => $message], 429);
            case 500:
                return response()->view('errors.500', ['message' => $message], 500);
            default:
                return response()->view('errors.generic', [
                    'message' => $message,
                    'status_code' => $statusCode
                ], $statusCode);
        }
    }

    /**
     * Get error code based on status code
     */
    private function getErrorCode($statusCode)
    {
        $errorCodes = [
            400 => 'BAD_REQUEST',
            401 => 'UNAUTHORIZED',
            403 => 'FORBIDDEN',
            404 => 'NOT_FOUND',
            405 => 'METHOD_NOT_ALLOWED',
            413 => 'POST_TOO_LARGE',
            419 => 'CSRF_TOKEN_MISMATCH',
            422 => 'VALIDATION_FAILED',
            429 => 'TOO_MANY_REQUESTS',
            500 => 'INTERNAL_SERVER_ERROR',
            503 => 'SERVICE_UNAVAILABLE'
        ];

        return $errorCodes[$statusCode] ?? 'UNKNOWN_ERROR';
    }
}
