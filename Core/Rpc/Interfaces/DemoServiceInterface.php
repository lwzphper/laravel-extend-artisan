<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/12/6 21:42,
 * @LastEditTime: 2021/12/6 21:42
 */
declare(strict_types=1);

namespace Core\Rpc\Interfaces;

/**
 * Interface UserServiceInterface
 * @package Core\Rpc\Interfaces
 * 用户信息
 */
interface DemoServiceInterface
{
    /**
     * 获取列表
     * @param array $params 参数
     * @param array
     * @return mixed
     */
    public function getList(array $params): array;
}
