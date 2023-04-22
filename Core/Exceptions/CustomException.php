<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/12/15 14:52,
 * @LastEditTime: 2021/12/15 14:52
 */

namespace Core\Exceptions;

use Illuminate\Http\JsonResponse;
use Core\Library\ApiResponse;

/**
 * Class CustomException
 * @package Core\Exceptions
 * @author lwz
 * 自定义异常
 */
class CustomException extends BasicException
{
    public array $extData; // 额外的数据，用于数据给 try catch 传递数据

    protected $message = 'error';

    public function __construct($errInfo = [], array $extData = [])
    {
        $errInfo = is_array($errInfo) ? $errInfo : [(string)$errInfo];
        $this->extData = $extData;
        parent::__construct($errInfo[0] ?? $this->getMessage(), $errInfo[1] ?? ($this->getCode() ?: 1060), $errInfo[2] ?? null);

    }

    /**
     * Render an exception into an HTTP response.
     * @param \Illuminate\Http\Request $request
     * @return mixed
     * @author lwz
     */
    public function render($request): JsonResponse
    {
        return ApiResponse::customError($this->getMessage(), $this->getCode(), $this->extData);
    }
}
