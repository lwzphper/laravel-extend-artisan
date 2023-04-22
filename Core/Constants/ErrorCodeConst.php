<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/12/15 11:40,
 * @LastEditTime: 2021/12/15 11:40
 */

namespace Core\Constants;

/**
 * Class ErrorCodeConst
 * @package App\Clue\Constants
 * @author lwz
 * 错误编号管理
 */
class ErrorCodeConst
{
    /**
     * 参数固定格式：
     * [ 异常信息 异常编号 前置异常信息]
     */
    public const GET_SUBORDINATE_ERROR = ['[无权操作] 当前用户非主管或管理员', 10001];

}
