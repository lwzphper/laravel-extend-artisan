<?php

namespace Core;


use Illuminate\Routing\Controller;

class CoreController extends Controller
{

    /**
     * 用户id
     * @var int
     */
    protected int $userId;

    /**
     * 用户名称
     * @var string
     */
    protected string $username;

    /**
     * 设置用户信息
     * @author lwz
     */
    protected function setUserInfo(): array
    {
        return [];
    }

    /**
     * 获取用户信息
     * @return array
     */
    protected function getUserInfo(): array
    {
        return [
            'id' => '1',
            'name' => 'admin',
        ];
    }
}
