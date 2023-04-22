<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/11/01 11:48,
 * @LastEditTime: 2021/11/01 11:48
 */

namespace Core\Exceptions;

use Illuminate\Http\JsonResponse;
use Core\Library\ApiResponse;

/**
 * Class DBException
 * @package Core\Exceptions
 * @author lwz
 * 数据库参数校验异常
 */
class DBParamValidException extends \Exception
{
//    protected $code = 1004;
//    protected $message = 'DB param error';

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
