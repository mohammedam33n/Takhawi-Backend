<?php

namespace App\Exceptions;

use App\DTOs\JSONApiError;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if (request()->segment(1) == 'api') {
            $code = $exception->getCode();
            $message = $exception->getMessage();

            // Errors Page Not Found
            if ($code < 100 || $code >= 600) {
                $code = ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
                // $message = ["message" => "NOT FOUND"];
                // return response()->json([
                //     'error_code'    => 1,
                //     'data'    => [],
                //     "message" => [$message]
                // ], 404);
            }

            // Errors Unauthenticated
            if ($exception instanceof AuthenticationException) {
                $message = ["message" => "Unauthenticated"];
                return response()->json([
                    'error_code'    => 1,
                    'data'    => [],
                    "message" => [$message]
                ], 401);
            }
        } else {
            // Errors Unauthenticated
            if ($exception instanceof AuthenticationException) {
                return redirect()->route('login');
            }
            return parent::render($request, $exception);
        }
    }
}
