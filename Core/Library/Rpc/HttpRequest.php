<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/12/02 11:48,
 * @LastEditTime: 2021/12/02 11:48
 */

namespace Core\Library\Rpc;

use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Core\Constants\RpcConst;
use Core\Exceptions\RpcException;
use Core\Interfaces\RpcRequestInterface;

class HttpRequest implements RpcRequestInterface
{
    /**
     * 响应的编号
     * @var int
     */
    protected int $rspSuccessCode = 0;

    /**
     * 响应的编号的字段
     * @var string
     */
    protected string $rspCodeField = 'code';

    /**
     * 响应的消息字段
     * @var string
     */
    protected string $rspMsgField = 'msg';

    /**
     * 响应的数据字段
     * @var string
     */
    protected string $rspDataField = 'data';

    /**
     * 接口名称
     * @var string|null
     */
    protected ?string $apiName = null;

    /**
     * 是否获取原始数据
     * @var bool
     */
    protected bool $getOriData = false;

    /**
     * 缓存key前缀
     * @var string
     */
    protected string $cachePrefix = 'rpc:';

    /**
     * 请求超时时间
     * @var int
     */
    protected int $timeout = 5;

    /**
     * 请求主机地址
     * @var string
     */
    protected string $host;

    public function __construct()
    {
        // 设置主机地址
        $this->host = config('app.url');
    }

    /**
     * 设置响应的编号
     * @param int $code 请求编号
     * @return $this
     * @author lwz
     */
    public function setRspSuccessCode(int $code): HttpRequest
    {
        $this->rspSuccessCode = $code;
        return $this;
    }

    /**
     * 设置响应编号字段
     * @param string $field 编号字段
     * @return HttpRequest
     * @author lwz
     */
    public function setRspCodeField(string $field): HttpRequest
    {
        $this->rspCodeField = $field;
        return $this;
    }

    /**
     * 设置响应的数据字段
     * @param string $field 数据字段名
     * @return $this
     * @author lwz
     */
    public function setRspDataField(string $field): HttpRequest
    {
        $this->rspDataField = $field;
        return $this;
    }

    /**
     * 设置响应的消息字段
     * @param string $field 消息字段名
     * @return $this
     * @author lwz
     */
    public function setRspMsgField(string $field): HttpRequest
    {
        $this->rspMsgField = $field;
        return $this;
    }

    /**
     * 修改超时时间
     * @param int $time
     * @return $this
     * @author lwz
     */
    public function setTimeout(int $time): HttpRequest
    {
        $this->timeout = $time;
        return $this;
    }

    /**
     * 设置缓存key前缀
     * @param string $prefix 前缀
     * @return HttpRequest
     * @author lwz
     */
    public function setCachePrefix(string $prefix): HttpRequest
    {
        $this->cachePrefix = $prefix;
        return $this;
    }

    /**
     * 设置是否返回原始数据
     * @param bool $bool
     * @return HttpRequest
     * @author lwz
     */
    public function setOriData(bool $bool): HttpRequest
    {
        $this->getOriData = $bool;
        return $this;
    }

    /**
     * 设置接口名称
     * @param string|null $apiName 接口名称
     * @return $this
     */
    public function setApiName(?string $apiName): HttpRequest
    {
        $this->apiName = $apiName;
        return $this;
    }

    /**
     * 发送请求
     * @param string $uri uri地址
     * @param array $params 请求参数
     *  _host: 请求域名
     *  _method：请求方式，get、post、put...
     *  _token：token参数
     * @param int|null $cacheTime 缓存时间(如果没传直接调用接口，如果传了先查缓存再调接口)
     * @param bool $saveRequestAttr 是否保存在请求对象属性中，防止接口调用多次
     * @return mixed
     * @throws RpcException|\Psr\SimpleCache\InvalidArgumentException
     * @author lwz
     */
    public function send(string $uri, array $params = [], ?int $cacheTime = null, bool $saveRequestAttr = false)
    {
        // 获取缓存key
        $cacheKey = $this->getCacheKey($uri, $params);

        // 从请求属性中获取数据
        if ($saveRequestAttr && $data = request()->attributes->get($cacheKey)) {
            return $this->getResultData($data);
        }

        // 文件上传不缓存数据
        if ($cacheTime) { // 设置了缓存时间的情况
            $data = Cache::get($cacheKey);
            if (is_null($data)) {
                $data = $this->handleRequest($uri, $params);
                Cache::set($cacheKey, $data, $cacheTime);
            }
        } else {
            $data = $this->handleRequest($uri, $params);
        }

        // 设置请求属性
        $saveRequestAttr && request()->attributes->set($cacheKey, $data);

        // 返回数据
        return $this->getResultData($data);
    }

    protected function getResultData($data)
    {
        return $this->getOriData ? $data : $data[$this->rspDataField];
    }

    /**
     * 获取缓存
     * @param string $cacheKey 缓存key
     * @param string|null $cacheType 缓存类型
     * @return mixed
     * @author lwz
     */
    protected function getCache(string $cacheKey, ?string $cacheType)
    {
        switch ($cacheType) {
            case RpcConst::CACHE_TYPE_REQUEST_ATTR:
                return request()->attributes->get($cacheKey);
            default: // 默认走redis
                return Cache::get($cacheKey);
        }
    }

    /**
     * 设置缓存
     * @param string $cacheKey 缓存key
     * @param mixed $data 缓存数据
     * @param string|null $cacheType 缓存类型
     * @param int|null $cacheTime 缓存时间
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @author lwz
     */
    protected function setCache(string $cacheKey, $data, ?string $cacheType, ?int $cacheTime = null): void
    {
        switch ($cacheType) {
            case RpcConst::CACHE_TYPE_REQUEST_ATTR:
                request()->attributes->set($cacheKey, $data);
                break;
            default: // 默认走redis
                Cache::set($cacheKey, $data, $cacheTime);
        }
    }

    /**
     * 文件上传
     * @param string $uri uri地址
     * @param array $fileParams 文件上传的相关参数
     *    必填项：
     *      name：接受文件的名字（相当于 HTML Input 的 name 属性）
     *      contents：文件内容
     *    选填项：
     *      filename: 文件名
     *      headers：请求头数组
     * @return mixed
     * @throws RpcException
     * @author lwz
     */
    public function uploadFile(string $uri, array &$fileParams)
    {
        $data = $this->handleRequest($uri, $fileParams, true);
        return $this->getOriData ? $data : $data[$this->rspDataField];
    }

    /**
     * 处理请求
     * @param string $uri uri地址
     * @param array $params 请求参数
     *  _host: 请求域名
     *  _method：请求方式，get、post、put...
     *  _token：token参数
     * @throws RpcException
     * @author lwz
     */
    protected function handleRequest(string $uri, array &$params, bool $uploadFile = false)
    {
        $host = $params['_host'] ?? null;
        $token = $params['_token'] ?? null; // 获取token
        $method = $params['_method'] ?? 'get';
        $header = $params['_header'] ?? [];

        $http = Http::timeout($this->timeout);
        $token && $http = $http->withToken($token); // 设置token
        $header && $http = $http->withHeaders($header);

        $url = $this->getUrl($uri, $host);

        // 记录开始时间
        $startTime = microtime(true); // 开始时间

        try {
            /**
             * @var $http Response
             */
            if ($uploadFile) {
                $multiParams = [];
                foreach ($params as $key => $val) {
                    if (is_array($val)) { // 跳过值为数组的值
                        throw new RpcException('multipart格式不支持数组参数');
                    }
                    array_push($multiParams, [
                        'name' => $key,
                        'contents' => $val,
                    ]);
                    unset($params[$key]);
                }
                $http = $http->asMultipart()->post($url, $multiParams);
            } else {
                $http = $http->$method($url, $params, ['verify' => false]);
            }
        } catch (HttpClientException $e) {
            throw new RpcException($this->getErrMsg($uri, $e->getMessage()));
        }

        if (!$http->ok()) {
            throw new RpcException($this->getErrMsg($uri, ' [status code]：' . $http->status()));
        }

        $response = $http->json();

        // 检查远程接口响应是否正确
        if ($response[$this->rspCodeField] != $this->rspSuccessCode) {
            throw new RpcException($this->getErrMsg($uri, $response[$this->rspMsgField] ?? ''));
        }

        // 记录请求慢的接口
        $endTime = microtime(true) - $startTime;
        if ($endTime > 1) {
            Log::info($uri . '接口请求慢' . $endTime);
        }

        return $response;
    }

    /**
     * 获取请求url
     * @param string|null $uri uri地址
     * @param string|null $host 请求域名
     * @return string
     * @author lwz
     */
    protected function getUrl(?string $uri, ?string $host = null): string
    {
        return ($host ?? (config('app.url'))) . '/' . ltrim($uri, '/');
    }

    /**
     * 获取错误信息
     * @param string $uri 请求uri
     * @param string $errMsg 错误信息
     * @return string
     * @author lwz
     */
    protected function getErrMsg(string $uri, string $errMsg): string
    {
        return '[http error] ' . ($this->apiName ?: $uri) . ' ' . $errMsg;
    }

    /**
     * 获取缓存key
     * @param string $uri uri地址
     * @param array $params 请求参数
     * @return string
     * @author lwz
     */
    protected function getCacheKey(string $uri, array $params): string
    {
        // todo 删除参数中每次请求都更新的字段，如：接口签名用到的timestamp


        // 删除签名和时间戳，
        ksort($params); // 对参数进行排序
        return $this->cachePrefix . md5($uri . json_encode($params));
    }
}
