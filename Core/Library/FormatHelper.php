<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/11/27 16:24,
 * @LastEditTime: 2021/11/27 16:24
 */

namespace Core\Library;

use Illuminate\Support\Collection;

/**
 * Class FormatHelper
 * @package Core\Library
 * @author lwz
 * 格式化辅助函数
 */
class FormatHelper
{
    /**
     * 获取分页列表响应数据
     * @param Collection|array $list 数据列表
     * @param int|null $pageNo 页码
     * @param int|null $pageSize 分页大小
     * @param int|null $total 数据总数
     * @return array
     * @author lwz
     */
    public static function getPageListResponse($list = [], ?int $pageNo = null, ?int $pageSize = null, ?int $total = null): array
    {
        $tmp = [];
        if (is_numeric($total)) {
            $tmp = [
                'total' => $total,
                'page_count' => self::getPageCount($total, $pageSize),
            ];
        }

        return array_merge($tmp, [
            'page' => $pageNo ?? 1,
            'size' => $pageSize ?? 0,
            'list' => $list
        ]);
    }

    /**
     * 格式 json
     * @param $data
     * @return string|false
     * @author lwz
     */
    public static function jsonEncodeUnescaped($data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * 获取总页数
     * @param int $total
     * @param ?int $pageSize
     * @return int
     * @author lwz
     */
    protected static function getPageCount(int $total, ?int $pageSize): int
    {
        return isset($pageSize) ? ceil($total / $pageSize) : 0;
    }

    /**
     * 获取子节点
     * @param array $list 数组列表
     * @param string $pk 主键字段名
     * @param string $pid 父级字段名
     * @param mixed $root 顶级节点的值（数据库parent_id的默认值）
     * @param bool $saveSelf 是否保存自身
     * @return array
     * @author lwz
     */
    public static function getChildNodes(array $list, string $pk = 'id', string $pid = 'pid', $root = 0, bool $saveSelf = true): array
    {
        static $arr = [];
        foreach ($list as $key => $item) {
            // 保存自身节点
            if ($saveSelf && $item[$pk] == $root) {
                $arr[] = $item;
                continue;
            }
            if ($item[$pid] == $root) {
                $arr[] = $item;
                unset($list[$key]); //注销当前节点数据，减少已无用的遍历
                self::getChildNodes($list, $pk, $pid, $item[$pk]);
            }
        }
        return $arr;
    }

    /**
     * 生成树结构
     * @param array $list 数组列表
     * @param string $pk 主键字段名
     * @param string $pid 父级字段名
     * @param mixed $root 顶级节点的值（数据库parent_id的默认值）
     * @param string $child 生成数组子级的命名
     * @return array
     * @author lwz
     */
    public static function getTree(array $list, string $pk = 'id', string $pid = 'pid', $root = null, string $child = 'child'): array
    {
        if (empty($list)) {
            return [];
        }

        // 如果最顶级id为null，则获取列表中 $pid 字段最小的值
        if (is_null($root)) {
            $root = min(array_column($list, $pid)) ?: 0;
        }


        // 创建Tree
        $tree = [];
        // 创建基于主键的数组引用
        $refer = [];
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] = &$list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent = &$refer[$parentId];
                    $parent[$child][] = &$list[$key];
                }
            }
        }
        return $tree;
    }

    /**
     * 获取下拉框相应结果
     * @param array $list
     * @return array
     * @author lwz
     */
    public static function getSelectResponse(array $list): array
    {
        $ret = [];
        foreach ($list as $id => $name) {
            $ret[] = compact('id', 'name');
        }
        return $ret;
    }
}
