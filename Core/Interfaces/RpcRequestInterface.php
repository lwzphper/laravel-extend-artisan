<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/12/02 11:06,
 * @LastEditTime: 2021/12/02 11:06
 */

namespace Core\Interfaces;

/**
 * Interface RpcInterface
 * @package Core\Interfaces
 * rpc 统一接口
 */
interface RpcRequestInterface
{
    /**
     * 设置接口名称
     * @param string|null $apiName 接口名称
     * @return RpcRequestInterface
     */
    public function setApiName(?string $apiName): RpcRequestInterface;

    /**
     * 发送请求
     * @param string $uri uri地址
     * @param array $params 请求参数
     * @param int|null $cacheTime 缓存时间(如果没传直接调用接口，如果传了先查缓存再调接口)
     * @param bool $saveRequestAttr 是否保存在请求对象属性中，防止接口调用多次
     * @return mixed
     * @author lwz
     */
    public function send(string $uri, array $params = [], ?int $cacheTime = null, bool $saveRequestAttr = false);

    /**
     * 文件上传
     * @param string $uri uri地址
     * @param array $fileParams 文件上传的相关参数
     * @return mixed
     * @author lwz
     */
    public function uploadFile(string $uri, array &$fileParams);
}
