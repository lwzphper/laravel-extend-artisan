<?php

namespace Core\Exceptions;

use Illuminate\Support\Facades\Redis;
use Core\Library\ApiResponse;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Routing\Router;
use Core\Library\QywxApiHelper;
use Core\Sign\Exceptions\SignException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Liujun\Auth\Exceptions\UnauthorizedException;

class Handler extends ExceptionHandler
{

    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected $dontReport = [
        ValidateException::class,
        MethodNotAllowedHttpException::class,
        BasicException::class,
        UnauthorizedException::class,
        \InvalidArgumentException::class,
        SignException::class,
        DBParamValidException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
     */
    protected $dontFlash = [
        'password',
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

    public function render($request, Throwable $e)
    {
        // 返回原始异常信息的情况
        if ($e->showOriMsg ?? null) {
            return ApiResponse::serviceError($e->getMessage(), $e->getCode());
        }

        if (config('app.debug')) {
            return parent::render($request, $e);
        }

        // 处理登录异常错误
        if ($e instanceof UnauthorizedException) {
            return ApiResponse::unauthorizedError($e->getMessage(), $e->getCode());
        }

        // 处理 http 异常请求
        if ($e instanceof NotFoundHttpException) { // 404 路由找不到
            return ApiResponse::httpNotFoundError();
        }

        // 方法不支持
        if ($e instanceof MethodNotAllowedHttpException) {
            return ApiResponse::httpMethodError();
        }

        // 优先调用异常类的 render 方法
        if (method_exists($e, 'render') && $response = $e->render($request)) {
            return Router::toResponse($request, $response);
        } elseif ($e instanceof Responsable) {
            return $e->toResponse($request);
        }

        // 发送异常消息
        QywxApiHelper::sendErrorMsg($e);

        return ApiResponse::serviceError();
    }
}
