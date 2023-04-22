<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/11/25 18:12,
 * @LastEditTime: 2021/11/25 18:12
 */

namespace Core\Constants;

/**
 * Class DBConst
 * @package Core\Constants
 * @author lwz
 * 数据库相关常量
 */
class DBConst
{
    // 结果最大返回数量
    public const RESULT_MAX_SIZE = 500;

    // id 分隔符
    public const ID_LIMITER = ',';

    // 升降序排序的健名
    public const ORDER_ASC = 'ASC';
    public const ORDER_DESC = 'DESC';
    public static array $orderKey = [
        self::ORDER_ASC,
        self::ORDER_DESC,
    ];

    // 状态
    public const DISABLE = 0; // 关闭
    public const ENABLE = 1; // 启用
    public static array $onOff = [
        self::DISABLE,
        self::ENABLE,
    ];

    // 字段类型的默认值
    public const DEFAULT_INT = 0;
}
