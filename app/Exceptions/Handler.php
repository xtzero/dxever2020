<?php

namespace App\Exceptions;

use App\Support\LogSupport;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        $traceArr = [];
        foreach($exception->getTrace() as $v) {
            $traceArr[] = json_encode($v);
        }
        LogSupport::error(implode("\n", $traceArr));
        
        return response()->json([
            'code' => 500,
            'msg' => $exception->getMessage(),
            'data' => []
        ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }
}
