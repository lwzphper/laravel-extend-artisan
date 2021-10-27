<?php
namespace Lwz\LaravelExtend\Artisan\Make;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

trait GeneratorCommand{

    protected function rootNamespace()
    {
        return config('extend.artisan.package.namespace');
    }

    public function getPackagePath()
    {
        return config('extend.artisan.package.path');
    }

    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);
        return $this->getPackagePath().str_replace('/', '\\', $name).'.php';
    }

    protected function getArguments()
    {
        return [
            ['package', InputArgument::REQUIRED, 'The package of the class'],
            ['name', InputArgument::REQUIRED, 'The name of the class'],
        ];
    }

    protected function getPackageInput()
    {
        return str_replace('/', '\\', trim($this->argument('package')));
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\\'.$this->getPackageInput().$this->namespaceSuffix;
    }

    protected function packageRootNamespace()
    {
        return $this->rootNamespace().'\\'.$this->getPackageInput()."\\";
    }

    protected function replaceNamespace(&$stub, $name)
    {
        $stub = str_replace(
            ['DummyNamespace', 'DummyRootNamespace', 'NamespacedDummyUserModel'],
            [$this->getNamespace($name), $this->packageRootNamespace(), $this->userProviderModel()],
            $stub
        );

        return $this;
    }
}
