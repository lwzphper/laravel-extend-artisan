<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/10/27 16:03,
 * @LastEditTime: 2021/10/27 16:03
 */

namespace Lwz\LaravelExtend\Artisan;


use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Lwz\LaravelExtend\Artisan\Make\Controller;
use Lwz\LaravelExtend\Artisan\Make\MigrateCommand;
use Lwz\LaravelExtend\Artisan\Make\MigrateMakeCommand;
use Lwz\LaravelExtend\Artisan\Make\MigrateResetCommand;
use Lwz\LaravelExtend\Artisan\Make\MigrateRollbackCommand;
use Lwz\LaravelExtend\Artisan\Make\MircoService;
use Lwz\LaravelExtend\Artisan\Make\Model;
use Lwz\LaravelExtend\Artisan\Make\RequestMakeCommand;

class ArtisanServiceProvider extends ServiceProvider
{
    protected array $command = [
        MircoService::class,
        Controller::class,
        Model::class,
        RequestMakeCommand::class,
    ];

    // 迁移文件注册命令
    protected array $migrateRegisterCommands = [
        'MigrateMake' => 'ext.command.migrate.make',
        'Migrate' => 'ext.command.migrate',
        // 禁用 reset 和 rollback ，防止数据出错
//        'MigrateReset' => 'ext.command.migrate.reset',
//        'MigrateRollback' => 'ext.command.migrate.rollback',
    ];

    public function register()
    {
        // 注册命令
        $this->commands($this->command);
        // 注册迁移文件命令
        $this->migrateRegisterCommands($this->migrateRegisterCommands);

        // 注册配置文件
        $this->mergeConfigFrom(
            __DIR__ . '/Config/artisan.php', 'extend.artisan'
        );

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

    /**
     * 注册迁移文件
     *
     * @param array $commands
     * @return void
     */
    protected function migrateRegisterCommands(array $commands)
    {
        foreach (array_keys($commands) as $command) {
            $this->{"register{$command}Command"}();
        }

        $this->commands(array_values($commands));
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerMigrateMakeCommand()
    {
        $this->app->singleton('ext.command.migrate.make', function ($app) {
            // Once we have the migration creator registered, we will create the command
            // and inject the creator. The creator is responsible for the actual file
            // creation of the migrations, and may be extended by these developers.
            $creator = $app['migration.creator'];

            $composer = $app['composer'];

            return new MigrateMakeCommand($creator, $composer);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerMigrateCommand()
    {
        $this->app->singleton('ext.command.migrate', function ($app) {
            return new MigrateCommand($app['migrator'], $app[Dispatcher::class]);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerMigrateResetCommand()
    {
        $this->app->singleton('ext.command.migrate.reset', function ($app) {
            return new MigrateResetCommand($app['migrator']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerMigrateRollbackCommand()
    {
        $this->app->singleton('ext.command.migrate.rollback', function ($app) {
            return new MigrateRollbackCommand($app['migrator']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array_merge([
            'migrator', 'migration.repository', 'migration.creator',
        ], array_values($this->migrateRegisterCommands));
    }

}