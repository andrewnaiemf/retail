<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use App\Traits\GeneralTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{

    use GeneralTrait;

    public function render($request, Throwable $exception)
    {

        if ($exception instanceof HttpException || $exception instanceof NotFoundHttpException || $exception instanceof MethodNotAllowedHttpException || $exception instanceof HttpResponseException) {

            if (  $request->is('api/*')  ) {

                if ($exception instanceof ModelNotFoundException) {
                    return $this->returnError( 404, trans('auth.Not_found'));
                }

                if ($exception->getMessage() == 'Unauthenticated'){
                    return $this->unauthorized();
                }
            }
        }

        return $this->returnError( 404, $exception->getMessage());
    }

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
