<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/11/27 13:53,
 * @LastEditTime: 2021/11/27 13:53
 */

namespace Core\Exceptions;


use Illuminate\Http\JsonResponse;
use Core\Library\ApiResponse;

/**
 * Class DataNotFoundException
 * @package Core\Exceptions
 * @author lwz
 * 数据不存在异常
 */
class DataNotFoundException extends BasicException
{
    public function render($request): JsonResponse
    {
        return ApiResponse::dataNotFoundError($this->getMessage(), $this->getCode());
    }
}
