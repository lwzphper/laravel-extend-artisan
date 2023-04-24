<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/11/23 15:14,
 * @LastEditTime: 2021/11/23 15:14
 */

namespace Lwz\LaravelExtend\Artisan\Make;

use Illuminate\Database\Console\Migrations\MigrateCommand as command;

class MigrateCommand extends command
{
    use CommandTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ext-migrate {service : The service of the migration} {--database= : The database connection to use}
                {--force : Force the operation to run when in production}
                {--path=* : The path(s) to the migrations files to be executed}
                {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
                {--schema-path= : The path to a schema dump file}
                {--pretend : Dump the SQL queries that would be run}
                {--seed : Indicates if the seed task should be re-run}
                {--step : Force the migrations to be run so they can be rolled back individually}';


    /**
     * 获取迁移文件的路径
     * @return string
     * @author lwz
     */
    protected function getMigrationPath()
    {
        $serInput = $this->getServiceInput();
        if ($serDir = $this->getServiceDirName()) {
            $serInput = $serDir . '/' . $serInput;
        }
        return $this->laravel['path'] . '/' . $serInput . '/Database/' . 'migrations';
    }
}