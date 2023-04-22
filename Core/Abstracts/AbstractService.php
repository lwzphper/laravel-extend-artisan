<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/12/07 13:35,
 * @LastEditTime: 2021/12/07 13:35
 */

namespace Core\Abstracts;

abstract class AbstractService
{
    /**
     * 缓存对象
     * @var AbstractCache
     */
    protected AbstractCache $cacheObj;

    /**
     * 删除缓存
     * @param callable $handleFn 处理函数
     * @param array $params 参数
     * @return mixed
     */
    protected function deleteCache(callable $handleFn, array $params)
    {
        $this->handleRemoveCache($params);
        $result = $handleFn();
        $this->handleRemoveCache($params);
        return $result;
    }

    /**
     * 处理删除缓存的操作
     * @param array $params 参数
     * @return mixed
     * @author lwz
     */
    abstract public function handleRemoveCache(array &$params);
}
