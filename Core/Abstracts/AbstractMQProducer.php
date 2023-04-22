<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2022/05/16 10:26,
 * @LastEditTime: 2022/05/16 10:26
 */
declare(strict_types=1);

namespace Core\Abstracts;

use Lwz\LaravelExtend\MQ\Interfaces\MQReliableProducerInterface;

abstract class AbstractMQProducer
{
    protected array $payload = [];
    protected MQReliableProducerInterface $producerObj;

    public function getMQProducer(): MQReliableProducerInterface
    {
        return $this->producerObj;
    }

    /**
     * 简单的推送队列（不会记录消息状态，主要用户消息重新投递）
     * @return mixed
     * @author lwz
     */
    public function simplePublish()
    {
        return $this->producerObj->simplePublish($this->payload);
    }

    public function publishPrepare(array $payload)
    {
        $this->producerObj->publishPrepare($payload);
    }

    public function publishMessage()
    {
        $this->producerObj->publishMessage();
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function setPayload(array $payload)
    {
        $this->payload = $payload;
        return $this;
    }
}
