<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/11/27 13:47,
 * @LastEditTime: 2021/11/27 13:47
 */

namespace Core\Exceptions;

use Illuminate\Http\JsonResponse;
use Core\Library\ApiResponse;

/**
 * Class NotPermissionException
 * @package Core\Exceptions
 * @author lwz
 * Rpc异常
 */
class RpcException extends BasicException
{
    public function render($request): JsonResponse
    {
        return ApiResponse::serviceError($this->getMessage(), $this->getCode());
    }
}
