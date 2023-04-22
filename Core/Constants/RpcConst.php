<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/12/21 17:10,
 * @LastEditTime: 2021/12/21 17:10
 */

namespace Core\Constants;

/**
 * Class RpcConst
 * @package Core\Constants
 * @author lwz
 * rpc 请求常量
 */
class RpcConst
{
    // 缓存类型
    public const CACHE_TYPE_REDIS = 1; // redis
    public const CACHE_TYPE_REQUEST_ATTR = 2; // 请求对象属性（只对当前请求有效）
}
