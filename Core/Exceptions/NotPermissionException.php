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
 * 无权操作异常
 */
class NotPermissionException extends CustomException
{
    protected $code = 4002;

    protected $message = '';

    public function render($request): JsonResponse
    {
        return ApiResponse::notPermission($this->getMessage(), $this->getCode());
    }
}
