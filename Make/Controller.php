<?php

namespace Lwz\LaravelExtend\Artisan\Make;

use Illuminate\Routing\Console\ControllerMakeCommand as Command;

class Controller extends Command
{
//    use GeneratorCommand;

    protected $name = 'ext-make:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建控制器';

    /**
     * 创建控制器
     * @return bool|null
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @author lwz
     */
    public function handle()
    {
        $this->_createBaseController();
        return parent::handle();
    }

    /**
     * 创建 BaseController
     * @author lwz
     */
    private function _createBaseController()
    {
        // 补全类的命名空间
        $name = $this->qualifyClass($this->qualifyNameDir('BaseController'));
        // 获取文件绝对路径
        $path = $this->getPath($name);

        $this->makeDirectory($path);

        $this->files->put($path, $this->sortImports($this->buildBaseClass($name)));
    }

    protected function buildBaseClass($name)
    {
        $stub = $this->files->get($this->getBaseStub()); // stub 绝对路径
        return $this->replaceBaseClassNamespace($stub, $name)->replaceClass($stub, $name);
    }

    // 补全文件的目录名
    protected function qualifyNameDir($name): string
    {
        $pos = strpos(str_replace('\\', '/', $this->getNameInput()), '/');
        if ($pos !== false) {
            return substr($this->getNameInput(), 0, $pos + 1) . $name;
        }
        return $name;
    }

    /**
     * 获取stub模板的绝对路径
     * @return string
     * @author lwz
     */
    protected function getBaseStub(): string
    {
        return $this->resolveStubPath('/stubs/baseController.stub');
    }

    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.$stub;
    }

    protected function replaceBaseClassNamespace(&$stub, $name)
    {
        $searches = [
            ['DummyNamespace', 'DummyRootNamespace', 'NamespacedDummyUserModel'],
            ['{{ namespace }}', '{{ rootNamespace }}', '{{ namespacedUserModel }}'],
            ['{{namespace}}', '{{rootNamespace}}', '{{namespacedUserModel}}'],
        ];

        foreach ($searches as $search) {
            $stub = str_replace(
                $search,
                [$this->getNamespace($name), $this->rootNamespace(), $this->userProviderModel()],
                $stub
            );
        }

        return $this;
    }
}
