<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2022/02/15 15:21,
 * @LastEditTime: 2022/02/15 15:21
 */

namespace Core\Interfaces;

/**
 * Interface ConsumeLogInterface
 * @package Core\Interfaces
 * 消费记录接口
 */
interface ConsumeLogInterface
{
    /**
     * 检查唯一标识是否存在
     * @param string $uuid 唯一标识
     * @return bool
     * @author lwz
     */
    public function checkUuidExist(string $uuid): bool;

    /**
     * 添加数据
     * @param string $uuid 唯一标识
     * @return mixed
     * @author lwz
     */
    public function addData(string $uuid);
}
