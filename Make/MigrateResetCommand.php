<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/11/23 17:22,
 * @LastEditTime: 2021/11/23 17:22
 */

namespace Lwz\LaravelExtend\Artisan\Make;


use Illuminate\Database\Console\Migrations\ResetCommand;
use Illuminate\Database\Migrations\Migrator;
use Symfony\Component\Console\Input\InputArgument;

class MigrateResetCommand extends ResetCommand
{
    use CommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ext-migrate:reset';

    public function __construct(Migrator $migrator)
    {
        parent::__construct($migrator);

        $this->addArgument('service', InputArgument::REQUIRED, 'service name');
    }
}