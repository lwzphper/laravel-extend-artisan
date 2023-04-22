<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/12/6 21:40,
 * @LastEditTime: 2021/12/6 21:40
 */
declare(strict_types=1);

namespace Core\Rpc\Services;

use Core\Exceptions\ValidateException;
use Core\Interfaces\RpcRequestInterface;
use Core\CoreHttpSignService;
use Core\Library\ArrMacro;
use Core\Rpc\Interfaces\DemoServiceInterface;

class DemoService extends CoreHttpSignService implements DemoServiceInterface
{
    public function __construct(RpcRequestInterface $request)
    {
        parent::__construct($request);
        $this->apiHost = '192.168.10.110'; // 设置接口请求ip
    }

    /**
     * 获取列表
     * @param array $params 参数
     * @return array
     * @throws ValidateException
     */
    public function getList(array $params): array
    {
        return $this->sendRequest(
            $params,
            '/xxxx/xxxx/xxx',
            'xxx模块获取列表',
            30
        );
    }
}
