<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/11/27 10:31,
 * @LastEditTime: 2021/11/27 10:31
 */

namespace Core\Exceptions;

use Illuminate\Http\JsonResponse;
use Core\Constants\ErrorCodeConst;
use Core\Library\ApiResponse;

/**
 * Class AuthException
 * @package Core\Exceptions
 * @author lwz
 * 登录异常（如：登录失败、token错误）
 */
class UnauthorizedException extends BasicException
{
    public function render($request): JsonResponse
    {
        return ApiResponse::unauthorizedError($this->getMessage(), $this->getCode());
    }
}
