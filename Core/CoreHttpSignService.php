<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/12/6 21:49,
 * @LastEditTime: 2021/12/6 21:49
 */
declare(strict_types=1);

namespace Core;

use Illuminate\Support\Facades\Http;
use Core\Interfaces\RpcRequestInterface;

/**
 * Class CoreHttpService
 * @package Core
 * @author lwz
 * Core 签名请求接口
 */
class CoreHttpSignService
{
    /**
     * 接口请求地址
     * @var string
     */
    protected string $apiHost = '127.0.0.1';

    /**
     * 签名类型
     * @var string|null
     */
    protected ?string $signType = null;

    /**
     * RPC请求类
     * @var RpcRequestInterface
     */
    protected RpcRequestInterface $request;

    public function __construct(RpcRequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * 发送请求
     * @param array $params 请求参数
     * @param string $apiUri 接口uri
     * @param string|null $apiName 接口名称
     * @param int|null $cacheTime 缓存时间
     * @param bool $saveRequestAttr 是否保存在请求对象属性中，防止接口调用多次
     * @return mixed
     * @throws Exceptions\ValidateException
     * @author lwz
     */
    protected function sendRequest(array $params, string $apiUri, ?string $apiName = null, ?int $cacheTime = null, bool $saveRequestAttr = false)
    {
        $this->setParamsApiHost($params); // 设置host地址
        return $this->request->setApiName($apiName)
            ->send($apiUri, $this->getSignParams($params, $this->signType), $cacheTime, $saveRequestAttr);
    }

    /**
     * 发送请求（不带签名）
     * @param array $params 请求参数
     * @param string $apiUri 接口uri
     * @param string|null $apiName 接口名称
     * @param int|null $cacheTime 缓存时间
     * @param bool $saveRequestAttr 是否保存在请求对象属性中，防止接口调用多次
     * @return mixed
     * @author lwz
     */
    protected function sendRequestWithoutSign(array $params, string $apiUri, ?string $apiName = null, ?int $cacheTime = null, bool $saveRequestAttr = false)
    {
        $this->setParamsApiHost($params); // 设置host地址
        return $this->request->setApiName($apiName)
            ->send($apiUri, $params, $cacheTime, $saveRequestAttr);
    }

    /**
     * 文件上传
     * @param array $params 请求参数
     * @param string $apiUri 接口uri
     * @param string|null $apiName 接口名称
     * @throws Exceptions\ValidateException
     * @author lwz
     */
    protected function handleUploadFile(array $params, string $apiUri, ?string $apiName = null)
    {
        $this->setParamsApiHost($params); // 设置host地址
        // 文件上传暂不使用签名
        return $this->request->setApiName($apiName)
            ->uploadFile($apiUri, $params);
    }

    /**
     * 设置参数接口域名
     * @param array $params 请求参数
     */
    protected function setParamsApiHost(array &$params)
    {
        $params['_host'] = $this->apiHost;
    }
}
