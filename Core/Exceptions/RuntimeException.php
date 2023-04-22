<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/11/01 13:34,
 * @LastEditTime: 2021/11/01 13:34
 */

namespace Core\Exceptions;


use Illuminate\Http\JsonResponse;
use Core\Library\ApiResponse;

/**
 * Class ValidateBaseException
 * @package Core\Exceptions
 * @author lwz
 * 运行时错误
 */
class RuntimeException extends BasicException
{
    protected $code = 1070;

    /**
     * Render an exception into an HTTP response.
     * @param \Illuminate\Http\Request $request
     * @return mixed
     * @author lwz
     */
    public function render($request): JsonResponse
    {
        return ApiResponse::invalidArgument($this->getMessage(), $this->getCode());
    }
}
