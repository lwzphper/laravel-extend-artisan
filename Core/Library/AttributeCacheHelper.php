<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2023/03/09 16:07,
 * @LastEditTime: 2023/03/09 16:07
 */
declare(strict_types=1);

namespace Core\Library;

class AttributeCacheHelper
{
    private static array $attrCache = [];

    public static function setAttrCache(string $name, $value)
    {
        if (app()->runningInConsole()) {
            return;
        }
        self::$attrCache[$name] = $value;
    }

    public static function getAttrCache(string $name)
    {
        return self::$attrCache[$name] ?? null;
    }
}
