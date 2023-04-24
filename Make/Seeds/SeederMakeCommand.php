<?php

namespace Lwz\LaravelExtend\Artisan\Make\Seeds;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Database\Console\Seeds\SeederMakeCommand as Command;
use Illuminate\Support\Str;
use Lwz\LaravelExtend\Artisan\Make\CommandTrait;
use Symfony\Component\Console\Input\InputArgument;

class SeederMakeCommand extends Command
{
    use CommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ext-make:seeder';

    public function configure()
    {
        $this->addArgument('service', InputArgument::REQUIRED, 'service name');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/seeder.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param string $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return is_file($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . '/..' . $stub;
    }

    /**
     * Get the destination class path.
     *
     * @param string $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = str_replace('\\', '/', Str::replaceFirst($this->getNamespacePrefix(), '', $name));
        $databasePath = $this->getDatabasePath();
        if (is_dir($databasePath . '/seeds')) {
            return $databasePath . '/Seeds/' . $name . '.php';
        } else {
            return $databasePath . '/Seeders/' . $name . '.php';
        }
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param string $stub
     * @param string $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name)
    {
        // 替换命名空间
        $filePath = $this->getPath($name); // 获取目录路径
        $seederNamespace = str_replace('/', '\\', dirname($filePath)); // 将根目录，替换为根命名空间
        $seederNamespace = preg_replace('/^' . $this->rootDirname() . '\\\\/', $this->rootNamespace(), $seederNamespace, 1);
        $stub = str_replace(
            '{{ seederNamespace }}',
            $seederNamespace,
            $stub
        );
        return $this;
    }

    protected function getCreateModelName(): string
    {
        return '';
    }
}
