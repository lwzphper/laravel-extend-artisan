<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2022/02/15 15:23,
 * @LastEditTime: 2022/02/15 15:23
 */

namespace Core\Abstracts;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AbstractConsumeLog
 * @package Core\Abstracts
 * @author lwz
 * 消费日志
 */
abstract class AbstractConsumeLog
{
    /**
     * 仓库类
     */
    protected AbstractRepository $repository;

    /**
     * 唯一标识的key
     * @var string
     */
    protected string $uuidKey = 'mq_uuid';

    /**
     * 检查唯一标识是否存在
     * @param string $uuid 唯一标识
     * @return bool
     * @author lwz
     */
    public function checkUuidExist(string $uuid): bool
    {
        return !is_null($this->repository->getOneByWhere([$this->uuidKey => $uuid], ['id']));
    }

    /**
     * 添加数据
     * @param string $uuid 唯一标识
     * @return Model
     * @author lwz
     */
    public function addData(string $uuid): Model
    {
        return $this->repository->add([
            $this->uuidKey => $uuid,
        ]);
    }
}
