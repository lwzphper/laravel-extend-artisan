<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/11/01 13:52,
 * @LastEditTime: 2021/11/01 13:52
 */

namespace Core\Exceptions;

use Illuminate\Http\JsonResponse;
use Core\Library\ApiResponse;

/**
 * Interface ExceptionInterface
 * @package Core\Exceptions
 * 异常接口
 */
class BasicException extends \Exception
{
    /**
     * Render an exception into an HTTP response.
     * @param \Illuminate\Http\Request $request
     * @return mixed
     * @throws CustomException
     * @author lwz
     */
    public function render($request): JsonResponse
    {
        return ApiResponse::serviceError($this->getMessage(), $this->getCode());
    }
}
