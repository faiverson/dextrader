<?php

namespace App\Exceptions;

use Exception;
use Log;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\QueryException as QueryException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
//        HttpException::class,
//        ModelNotFoundException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
		Log::info('Exception!!!');
		if ($e instanceof Tymon\JWTAuth\Exceptions\TokenExpiredException) {
			return response()->error('The token has expired.' , $e->getStatusCode());
		}
		else if ($e instanceof Tymon\JWTAuth\Exceptions\TokenInvalidException) {
			return response()->error('The token is invalid.', $e->getStatusCode());
		}
		else if ($e instanceof ModelNotFoundException) {
			$e = new NotFoundHttpException($e->getMessage(), $e);
		}
//		else if ($e instanceof QueryException) {
//			return response()->error('Ups! There was a problem in database. Please contact support immediately!');
//		}
		else if ($e instanceof NotFoundHttpException) {
			return response()->error('Oops! The URL provided was not found!', $e->getStatusCode());
		}
//		else if ($this->isHttpException($e))
//		{
//			return response()->view('home');
//		}

//		return response()->error($e->getMessage(), $e->getStatusCode());
        return parent::render($request, $e);
    }

	protected function renderHttpException(HttpException $e)
	{
		//dd('asas');
		if (view()->exists('errors.' . $e->getStatusCode())) {
			response()->error($e->getMessage(), $e->getStatusCode());
			return response()->view('errors.'.$e->getStatusCode(), [], $e->getStatusCode());
		}
	}

}
