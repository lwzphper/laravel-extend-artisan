<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2022/01/22 15:09,
 * @LastEditTime: 2022/01/22 15:09
 */

namespace Core\Library;

/**
 * Class RandomHelper
 * @package Core\Library
 * @author lwz
 * 随机数生产辅助类型
 */
class RandomHelper
{
    /**
     * 生成模块编号
     * @param int $moduleNo 模块编号
     * @return string
     * @author lwz
     */
    public static function createModuleNo(int $moduleNo): string
    {
        // 规则：模块编号 + 年月日 + 6 位随机数
        return $moduleNo . date('Ymd') . strtoupper(self::randomStr());
    }

    /**
     * 生产指定长度随机数
     * @param int $length 随机数长度
     * @author lwz
     */
    protected static function randomStr(int $length = 6): string
    {
        // 随机字符
        $strings = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
        return substr(str_shuffle($strings), 0, $length);
    }
}
