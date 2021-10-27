<?php

namespace Lwz\LaravelExtend\Artisan\Make;

use Illuminate\Console\GeneratorCommand;

class MircoService extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'ext-make:micro';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建服务目录';

    // 服务跟目录名称
    protected string $microRootDirName = 'MicroServices';

    // 服务基本的目录名
    protected array $microBaseDirName = [
        'Interfaces' => [
            'serviceInterface.stub',
        ],
        'Models' => [
            'baseModel.stub',
            'serviceModel.stub',
        ],
        'Providers' => [
            'serviceRegisterProvider.stub'
        ],
        'Repositories' => [
            'repositoryAbstract.stub',
            'repository.stub',
        ],
        'Services' => [
            'service.stub'
        ],
    ];

    // stub对应生成的文件名
    protected array $stubToFileName = [
        'serviceInterface.stub' => '{{projectName}}ServiceInterface.php',
        'baseModel.stub' => 'BaseModel.php',
        'serviceModel.stub' => '{{projectName}}.php',
        'repository.stub' => '{{projectName}}Repository.php',
        'repositoryAbstract.stub' => 'RepositoryAbstract.php',
        'service.stub' => '{{projectName}}Service.php',
        'serviceRegisterProvider.stub' => '{{serviceName}}ServiceRegisterProvider.php',
    ];

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle(): void
    {
        // 分割服务名和项目名
        $inputArr = explode('/', $this->getNameInput());
        if (count($inputArr) != 2) {
            $this->error('参数格式有误。格式： 服务名/项目名');
            return;
        }

        $serviceName = ucfirst($inputArr[0]);
        $projectName = ucfirst($inputArr[1]);


        // 获取服务的目录路径（绝对路径）
        $basePath = $this->getMicroAbsolutePath() . '/' . $serviceName;

        /**
         * 创建目录 和 对应的文件
         */
        foreach ($this->microBaseDirName as $serviceDir => $stubs) {
            $serviceFullPath = $basePath . '/' . $serviceDir;
            $this->files->makeDirectory($serviceFullPath, 0777, true, true);
            $this->createFile($serviceFullPath, $stubs, $serviceName, $projectName);
        }

        // 打印提示信息
        $this->info('created successfully.');
    }

    /**
     * 创建文件
     * @param string $serviceFullPath
     * @param array $stubs
     * @param string $serviceName
     * @param string $projectName
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @author lwz
     */
    protected function createFile(string $serviceFullPath, array $stubs, string $serviceName, string $projectName)
    {
        foreach ($stubs as $stub) {
            $filePath = $this->getStubFilePath($serviceFullPath, $stub, $serviceName, $projectName);
            // 如果文件存在，不进行操作
            if (file_exists($filePath)) {
                continue;
            }
            $this->files->put($filePath, $this->sortImports($this->buildStubClass($serviceName, $projectName, $stub)));
        }
    }

    /**
     * 获取模板文件的路径
     * @param string $serviceFullPath 服务的目录地址
     * @param string $stubFileName stub文件名
     * @param string $serviceName
     * @param string $projectName 项目名称
     * @return string
     * @author lwz
     */
    protected function getStubFilePath(string $serviceFullPath, string $stubFileName, string $serviceName, string $projectName): string
    {
        return $serviceFullPath . '/' . str_replace(['{{projectName}}', '{{serviceName}}'], [$projectName, $serviceName], $this->stubToFileName[$stubFileName]);
    }

    /**
     * 构建类文件内容
     * @param string $projectName 项目名称
     * @param string $stubFileName stub模板文件名称
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @author lwz
     */
    protected function buildStubClass(string $serviceName, string $projectName, string $stubFileName)
    {
        $stub = $this->files->get($this->resolveStubPath('/stubs/' . $stubFileName));
        return str_replace(['{{ ServiceName }}', '{{ ProjectName }}', '{{ TableName }}'], [$serviceName, $projectName, \Str::snake($projectName)], $stub);
    }

    /**
     * 补全stub的绝对路径
     *
     * @param string $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;
    }

    /**
     * 获取服务绝对路径
     * @return string
     * @author lwz
     */
    protected function getMicroAbsolutePath(): string
    {
        return $this->laravel['path'] . '/' . str_replace('\\', '/', $this->microRootDirName);
    }


    /**
     * Replace the namespace for the given stub.
     *
     * @param string $stub
     * @param string $inputName 输入内容
     * @return $this
     */
    protected function replaceNamespace(&$stub, $inputName)
    {
        $searches = [
            ['{{ ServiceName }}', '{{ TableName }}']
        ];

        foreach ($searches as $search) {
            $stub = str_replace(
                $search,
                [$inputName, \Str::snake(lcfirst($inputName))],
                $stub
            );
        }

        return $this;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        // TODO: Implement getStub() method.
    }
}
