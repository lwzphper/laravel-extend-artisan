<?php

namespace Core\Providers;


use Illuminate\Support\ServiceProvider;
use Core\Interfaces\RpcRequestInterface;
use Core\Library\Rpc\HttpRequest;
use Core\Rpc\Interfaces\DemoServiceInterface;
use Core\Rpc\Services\DemoService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * 需要注册的服务提供者
     * @var array
     */
    protected array $providers = [
    ];

    /**
     * 自定义命令
     * @var array
     */
    protected array $command = [
    ];

    /**
     * Rpc 服务
     * @var array
     */
    protected array $rpcService = [
//        DemoServiceInterface::class => DemoService::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function register()
    {
        // rpc服务注册
        $this->app->instance(RpcRequestInterface::class, $this->app->make(HttpRequest::class));

        // 服务rcp
        foreach ($this->rpcService as $interface => $service) {
            $this->app->instance($interface, $this->app->make($service));
        }

        // 注册服务提供者
        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }

        // 自定义命令
        $this->command && $this->commands($this->command);
    }
}
