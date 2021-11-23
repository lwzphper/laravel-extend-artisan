<?php

namespace Lwz\LaravelExtend\Artisan\Make;

use Illuminate\Foundation\Console\ModelMakeCommand as Command;
use Symfony\Component\Console\Input\InputArgument;

class Model extends Command
{
    use CommandTrait;

    protected $name = 'ext-make:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建模型';

    public function configure()
    {
        $this->addArgument('service', InputArgument::REQUIRED, 'service name');
    }

    /**
     * 获取生成的模块名称
     * @return string
     * @author lwz
     */
    protected function getCreateModelName(): string
    {
        return 'Models';
    }
}
