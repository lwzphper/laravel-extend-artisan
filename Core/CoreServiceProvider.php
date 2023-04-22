<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/12/17 14:12,
 * @LastEditTime: 2021/12/17 14:12
 */

namespace Core;


use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Illuminate\Support\Facades\Event;

class CoreServiceProvider extends EventServiceProvider
{
    /**
     * rpc服务
     * @var array
     */
    protected array $rpcList = [
        // 本地服务

        // 远程服务
    ];

    /**
     * 服务列表
     * @var array|string[]
     */
    protected array $serviceList = [
    ];

    // 路由文件列表
    protected array $routes = [
        'routes.php'
    ];

    /**
     * 自定义命令
     * @var array
     */
    protected array $command = [

    ];

    /**
     * 事件
     * @var array
     */
    protected $listen = [
    ];

    // 初始化操作
    protected function init()
    {
        // 注册event
        ($this->listen || $this->subscribe) && $this->registerEvent();

        // 加载配置文件
        $this->loadConfig();

        // 服务rcp
        foreach ($this->rpcList as $interface => $service) {
            $this->app->instance($interface, $this->app->make($service));
        }

        // 服务注册
        foreach ($this->serviceList as $interface => $service) {
            $this->app->instance($interface, $this->app->make($service));
        }

        // 路由注册
        $this->registerRoutes();

        // 自定义命令
        $this->command && $this->commands($this->command);
    }


    /**
     * 加载配置文件
     * @author lwz
     */
    protected function loadConfig()
    {
        // 数据库配置
//        $this->mergeConfigFrom($this->_getConfigPath() . 'database.php', 'database.connections');
//        // 日志配置
//        $this->mergeConfigFrom($this->_getConfigPath() . 'logging.php', 'logging.channels');
    }

    /**
     * 注册路由
     */
    protected function registerRoutes()
    {
    }

    /**
     * 加载event事件
     * @author lwz
     */
    protected function registerEvent()
    {
        $this->booting(function () {
            $events = $this->getEvents();

            foreach ($events as $event => $listeners) {
                if (is_array($listeners)) {
                    foreach (array_unique($listeners) as $listener) {
                        Event::listen($event, $listener);
                    }
                } else {
                    Event::listen($event, $listeners);
                }
            }

            foreach ($this->subscribe as $subscriber) {
                Event::subscribe($subscriber);
            }
        });
    }
}
