<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/11/23 17:25,
 * @LastEditTime: 2021/11/23 17:25
 */

namespace Lwz\LaravelExtend\Artisan\Make;


use Illuminate\Database\Console\Migrations\RollbackCommand;
use Illuminate\Database\Migrations\Migrator;
use Symfony\Component\Console\Input\InputArgument;

class MigrateRollbackCommand extends RollbackCommand
{
    use CommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ext-migrate:rollback';

    public function __construct(Migrator $migrator)
    {
        parent::__construct($migrator);

        $this->addArgument('service', InputArgument::REQUIRED, 'service name');
    }


    /**
     * 获取迁移文件的路径
     * @return string
     * @author lwz
     */
    protected function getMigrationPath()
    {
        return $this->laravel['path'] . '/' . $this->getServiceDirName() . '/' . $this->getServiceInput() . '/Database/' . 'migrations';
    }
}