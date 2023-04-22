<?php
/**
 * @Author: chenkaijian <ikaijian@163.com>,
 * @Date: 2022/1/11 17:29,
 * @LastEditTime: 2022/1/11 17:29,
 * @Copyright: 2020 Core Inc. 保留所有权利。
 */

namespace Core\Traits;


use Illuminate\Support\Facades\Http;

trait RequestTrait
{
    /**
     * http请求
     *
     * @param string $url
     * @param array $params
     * @param string $method
     * @param array $headers
     * @return false|mixed
     * @Date: 2022/1/11 17:30
     * @Author: ikaijian
     */
    public function _httpRequest($url = '', $params = [], $method = 'get', $headers = [], $timeout = null)
    {
        if (empty($url)) {
            return false;
        }
        $method = strtolower($method);
        if (empty($headers)) {
            $headers = [
                'Content-Type' => ' application/json;charset=utf-8'
            ];
        }
        $httpObj = Http::withHeaders($headers);
        if ($timeout) {
            $httpObj = $httpObj->timeout($timeout);
        }
        if ($method === 'get') {
            $httpObj = $httpObj->get($url, $params);
        } else {
            $httpObj = $httpObj->post($url, $params);
        }
        $responseBody = $httpObj->body();
        return jsonDecodeUnion($responseBody);
    }
}
