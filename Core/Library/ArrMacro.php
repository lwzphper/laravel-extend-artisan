<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/08/23 11:31,
 * @LastEditTime: 2021/08/23 11:31
 */

namespace Core\Library;

use Illuminate\Support\Arr;

/**
 * Class ArrMacro
 * @package App\Common\Library
 * @author lwz
 * 数组对象的扩展方法
 */
class ArrMacro
{
    /**
     * 提取数组指定的key，并且将value转化为字符串
     * @param array $data
     * @param array $keys
     * @return array
     * @author lwz
     */
    public static function onlyToString(array $data, array $keys): array
    {
        return array_map(function ($item) {
            // 将数组转换成json（空数组，直接返回空字符串）
            if (is_array($item)) {
                $item = array_filter($item); // 防止数组中出现null的情况
                return empty($item) ? '' : json_encode($item);
            }
            // 将null转换成空字符串
            return $item ?? '';
        }, Arr::only($data, $keys));
    }

    /**
     * 提取集合中的key
     * @param array $data 数据
     * @param array|null $keys 需要获取的健
     * @return array
     * @author lwz
     */
    public static function onlyCollection(array $data, ?array $keys): array
    {
        // 如果没有设置keys，就直接返回原数据
        if (is_null($keys)) {
            return $data;
        }

        return array_map(function ($item) use ($keys) {
            return Arr::only($item, $keys);
        }, $data);
    }

    /**
     * 提取数组的字段作为健
     * @param array $data 数组
     * @param string $key 提取的字段
     * @return array
     * @author lwz
     */
    public static function pluckFieldToKey(array $data, string $key): array
    {
        $result = [];
        foreach ($data as $item) {
            $item = (array)$item; // 防止数组对象
            $result[$item[$key]] = $item;
        }
        return $result;
    }

    /**
     * 将null转换成空字符串
     * @param array $data 数据
     * @param array|null $onlyKeys 只获取指定的 key
     * @return array
     */
    public static function nullToString(array $data, ?array $onlyKeys = null): array
    {
        $onlyKeys && $data = Arr::only($data, $onlyKeys);
        return array_map(function ($item) {
            return $item ?? '';
        }, $data);
    }

    /**
     * 对比两个数组是否相等
     * @param array $data1 数组1
     * @param array $data2 数组2
     * @return bool
     * @author lwz
     */
    public static function checkEqual(array $data1, array $data2): bool
    {
        sort($data1);
        sort($data2);
        return $data1 == $data2;
    }

    /**
     * 获取数据交集
     * @param array $arr1
     * @param array $arr2
     * @param bool $resetIndex 是否重置索引
     * @return array
     * @author lwz
     */
    public static function getIntersect(array $arr1, array $arr2, bool $resetIndex = true): array
    {
        $intersect = array_intersect($arr1, $arr2);
        return $resetIndex ? array_values($intersect) : $intersect;
    }

    /**
     * 将数组的值转成int类型
     *
     * @param array $data
     * @return array
     * @Date: 2023/2/22 9:30
     * @Author: ikaijian
     */
    public static function arrValueToInt(array $data): array
    {
        if (empty($data)) {
            return [];
        }
        return array_map(function ($item) {
            return (int)$item;
        }, $data);
    }
}
