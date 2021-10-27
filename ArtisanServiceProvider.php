<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/10/27 16:03,
 * @LastEditTime: 2021/10/27 16:03
 */

namespace Lwz\LaravelExtend\Artisan;


use Illuminate\Support\ServiceProvider;
use Lwz\LaravelExtend\Artisan\Make\Controller;
use Lwz\LaravelExtend\Artisan\Make\MircoService;

class ArtisanServiceProvider extends ServiceProvider
{
    protected array $command = [
        MircoService::class,
        Controller::class,
    ];

    public function register()
    {
        $this->commands($this->command);

        // 发布模板文件
        $this->registerPublishing();
    }

    /**
     * 发布模板文件
     * @author lwz
     */
    protected function registerPublishing()
    {
        // 只有在 console 模式才执行
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/Make/stubs' => $this->app->basePath('stubs')
            ]);
        }
    }
}