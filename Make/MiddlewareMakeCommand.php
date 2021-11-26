<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/11/26 15:28,
 * @LastEditTime: 2021/11/26 15:28
 */

namespace Lwz\LaravelExtend\Artisan\Make;
use Illuminate\Routing\Console\MiddlewareMakeCommand as Command;
use Symfony\Component\Console\Input\InputArgument;

class MiddlewareMakeCommand extends Command
{
    use CommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ext-make:middleware';


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
        return 'Middleware';
    }

}