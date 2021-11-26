<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/11/26 10:47,
 * @LastEditTime: 2021/11/26 10:47
 */

namespace Lwz\LaravelExtend\Artisan\Make;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Console\RequestMakeCommand as command;
use Symfony\Component\Console\Input\InputArgument;

class RequestMakeCommand extends command
{
    use CommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ext-make:request';

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
        return 'Requests';
    }
}