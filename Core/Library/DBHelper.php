<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2022/5/16 22:12,
 * @LastEditTime: 2022/5/16 22:12
 */
declare(strict_types=1);

namespace Core\Library;


class DBHelper
{
    /**
     * 获取统计字段
     * @param array $fields 字段数组
     * @param string $aggFunc 聚合函数名称
     * @return string
     */
    public static function getStatFields(array $fields, string $aggFunc = 'sum'): string
    {
        $result = '';
        foreach ($fields as $field) {
            $result .= sprintf('%s(%s) %s,', $aggFunc, $field, $field);
        }
        return rtrim($result, ',');
    }

    /**
     * 设置字段表名
     * @param array $fields 字段数组
     * @param string $tableName 表名
     * @return array
     * @author lwz
     */
    public static function addFieldsTable(array $fields, string $tableName): array
    {
        return array_map(function ($field) use ($tableName) {
            return $tableName . '.' . $field;
        }, $fields);
    }
}
