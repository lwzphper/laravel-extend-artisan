<?php

namespace Lwz\LaravelExtend\Artisan\Make;

use Illuminate\Routing\Console\ControllerMakeCommand as Command;
use Symfony\Component\Console\Input\InputArgument;

class Controller extends Command
{
    use CommandTrait;

    protected $name = 'ext-make:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建控制器';

    public function configure()
    {
        $this->addArgument('service', InputArgument::REQUIRED, 'service name');
    }

    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;
    }
}
