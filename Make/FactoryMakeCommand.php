<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/11/27 18:00,
 * @LastEditTime: 2021/11/27 18:00
 */

namespace Lwz\LaravelExtend\Artisan\Make;

use Illuminate\Database\Console\Factories\FactoryMakeCommand as Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class FactoryMakeCommand extends Command
{
    use CommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ext-make:factory';

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
        return 'Database/factories';
    }

    protected function getPath($name)
    {
        $name = (string) Str::of($name)->replaceFirst($this->rootNamespace(), '')->finish('Factory');;
        return $this->laravel['path']  .'/'. str_replace('\\', '/', $name) . '.php';
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $factory = class_basename(Str::ucfirst(str_replace('Factory', '', $name)));

        $namespaceModel = $this->option('model')
            ? $this->qualifyModel($this->option('model'))
            : $this->qualifyModel($this->guessModelName($name));

        $model = class_basename($namespaceModel);

        if (Str::startsWith($namespaceModel, $this->rootNamespace().'Models')) {
            $namespace = Str::beforeLast('Database\\Factories\\'.Str::after($namespaceModel, $this->rootNamespace().'Models\\'), '\\');
        } else {
            $namespace = 'Database\\Factories';
        }

        $replace = [
            '{{ factoryNamespace }}' => $namespace,
            'NamespacedDummyModel' => $namespaceModel,
            '{{ namespacedModel }}' => $namespaceModel,
            '{{namespacedModel}}' => $namespaceModel,
            'DummyModel' => $model,
            '{{ model }}' => $model,
            '{{model}}' => $model,
            '{{ factory }}' => $factory,
            '{{factory}}' => $factory,
        ];

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }
}