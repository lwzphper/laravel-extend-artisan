<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/11/01 14:20,
 * @LastEditTime: 2021/11/01 14:20
 */

namespace Core\Library;

use Symfony\Component\HttpFoundation\Response as FoundationResponse;
use Illuminate\Http\JsonResponse;

class ApiResponse
{
    // 基础的状态码（系统默认状态码）
    protected static int $successCode = 0; // 请求成功的code值
    protected static int $invalidArgumentErrorCode = 400; // 请求参数验证失败的code值
    protected static int $unauthenticatedErrorCode = 401; // token无效(需要重新登录)
    protected static int $httpNotFoundErrorCode = 404; // 404请求错误码
    protected static int $serviceErrorCode = 501; // 服务端错误的code值
    protected static int $dataNotFoundCode = 4001; // 数据库数据没找到
    protected static int $notPermissionErrorCode = 4002; // 无权操作
    protected static int $customErrorCode = 4003; // 自定义异常错误
    protected static int $httpMethodErrorCode = 4004; // http请求方法错误
    protected static int $throttleErrorCode = 4005; // 限流异常

    /**
     * 返回结果的http状态码
     * @var int
     */
    protected static int $statusCode = FoundationResponse::HTTP_OK;

    /**
     * 请求头
     * @var array
     */
    protected static array $headers = [];

    /**
     * 获取返回结果的状态码
     * @return int
     */
    public static function getStatusCode(): int
    {
        return self::$statusCode;
    }

    /**
     * 设置状态码
     * @param int $statusCode 设置状态码
     */
    public static function setStatusCode(int $statusCode): ApiResponse
    {
        self::$statusCode = $statusCode;
        return (new self);
    }

    /**
     * 设置请求头
     * @param array $headers 请求头
     * @return static
     * @author lwz
     */
    public static function setHeader(array $headers): ApiResponse
    {
        self::$headers = $headers;
        return (new self);
    }

    /**
     * 成功的响应
     * @param mixed $data 响应数据
     * @param string|null $msg 消息
     * @return JsonResponse
     */
    public static function success($data = [], ?string $msg = null): JsonResponse
    {
        return self::respond(self::$successCode, $data, $msg);
    }

    /**
     * 无权操作
     * @param string|null $msg 错误消息
     * @param int|null $code 错误编号
     * @return JsonResponse
     * @author lwz
     */
    public static function notPermission(?string $msg = null, ?int $code = null): JsonResponse
    {
        $code = $code ?: self::$notPermissionErrorCode;
        return self::respond($code, [], $msg);
    }

    /**
     * 自定义异常错误
     * @param string|null $msg
     * @param int|null $code
     * @param array $extData 额外的数据
     * @return JsonResponse
     * @author lwz
     */
    public static function customError(?string $msg = null, ?int $code = null, array $extData = []): JsonResponse
    {
        $code = $code ?: self::$customErrorCode;
        return self::respond($code, $extData, $msg);
    }

    /**
     * 服务器异常响应
     * @param string|null $msg 错误消息
     * @param int|null $code 错误编号
     * @return JsonResponse
     */
    public static function serviceError(?string $msg = null, ?int $code = null): JsonResponse
    {
        $code = $code ?: self::$serviceErrorCode;
        return self::respond($code, [], $msg);
    }

    /**
     * 数据库数据未找到异常
     * @param string|null $msg 错误消息
     * @param int|null $code 错误编号
     * @return JsonResponse
     */
    public static function dataNotFoundError(?string $msg = null, ?int $code = null): JsonResponse
    {
        $code = $code ?: self::$dataNotFoundCode;
        return self::respond($code, [], $msg);
    }

    /**
     * http 请求方法有误
     * @param string|null $msg 错误消息
     * @param int|null $code 错误编号
     * @return JsonResponse
     */
    public static function httpMethodError(?string $msg = null, ?int $code = null): JsonResponse
    {
        $code = $code ?: self::$httpMethodErrorCode;
        return self::respond($code, [], $msg);
    }

    /**
     * 数据库数据未找到异常
     * @param string|null $msg 错误消息
     * @param int|null $code 错误编号
     * @return JsonResponse
     */
    public static function httpNotFoundError(?string $msg = null, ?int $code = null): JsonResponse
    {
        $code = $code ?: self::$httpNotFoundErrorCode;
        return self::respond($code, [], $msg);
    }

    /**
     * 限流异常
     * @param string|null $msg 错误消息
     * @param int|null $code 错误编号
     * @return JsonResponse
     */
    public static function throttleError(?string $msg = null, ?int $code = null): JsonResponse
    {
        $code = $code ?: self::$throttleErrorCode;
        return self::respond($code, [], $msg);
    }

    /**
     * 用户未认证异常（没登录或登录异常）
     * @param string|null $msg 错误消息
     * @param int|null $code 错误编号
     * @return JsonResponse
     * @author lwz
     */
    public static function unauthorizedError(?string $msg = null, ?int $code = null): JsonResponse
    {
        $code = $code ?: self::$unauthenticatedErrorCode;
        return self::respond($code, [], $msg);
    }

    /**
     * 返回参数错误响应
     * @param string|null $msg 错误消息
     * @param int|null $code 错误编号
     * @return JsonResponse
     */
    public static function invalidArgument(?string $msg = null, ?int $code = null): JsonResponse
    {
        $code = $code ?: self::$invalidArgumentErrorCode;
        return self::respond($code, [], $msg);
    }

    /**
     * 响应结果
     * @param int|null $code 结果代码
     * @param mixed $data 数据
     * @param string|null $msg 消息
     * @param int|null $statusCode
     * @return JsonResponse
     */
    protected static function respond(?int $code = null, $data = [], ?string $msg = null, ?int $statusCode = null): JsonResponse
    {
        $code = $code ?: self::$successCode;

        // authorization 'Bearer '.$token
        return response()->json(
            [
                'code' => $code,
                'msg' => $msg ?: self::getCodeMsg($code),
                'data' => is_null($data) ? [] : $data
            ],
            $statusCode ?? self::$statusCode,
            self::$headers
        );
    }

    /**
     * 获取编号对应的消息
     * @param int $code
     * @return string
     * @author lwz
     */
    protected static function getCodeMsg(int $code): string
    {
        switch ($code) {
            case self::$successCode:
                return '成功';
            case self::$invalidArgumentErrorCode:
                return '参数有误';
            case self::$unauthenticatedErrorCode:
                return '登录异常';
            case self::$httpNotFoundErrorCode:
                return '请求地址有误';
            case self::$serviceErrorCode:
                return '服务器异常，请稍后再试';
            case self::$dataNotFoundCode:
                return '数据不存在';
            case self::$customErrorCode:
                return '操作有误';
            case self::$httpMethodErrorCode:
                return 'http请求方法有误';
            case self::$notPermissionErrorCode:
                return '无权操作';
            case self::$throttleErrorCode:
                return '当前服务器负载过大，请稍后再试。';
        }
        return 'error';
    }
}
