<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/11/22 16:17,
 * @LastEditTime: 2021/11/22 16:17
 */

namespace Lwz\LaravelExtend\Artisan\Make;

use Illuminate\Support\Str;

trait CommandTrait
{
    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return config('extend.artisan.package.root_namespace');
    }

    /**
     * 获取服务目录名称
     * @return string
     * @author lwz
     */
    protected function getServiceDirName()
    {
        return config('extend.artisan.package.dir');
    }

    /**
     * Get the destination class path.
     *
     * @param string $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);
        return $this->laravel['path'] . '/' . str_replace('\\', '/', $name) . '.php';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        if ($service = $this->getServiceInput()) {
            $tmp = $rootNamespace . $this->getServiceDirName() . '\\' . $service;
            // 如果设置过了，就不设置
            if (!Str::startsWith($rootNamespace, $tmp)) {
                $rootNamespace = $tmp;
            }
        }
        return $rootNamespace . '\\' . $this->getCreateModelName();
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getServiceInput()
    {
        return trim($this->argument('service'));
    }

    /**
     * 获取生成的模块名称
     * @return string
     * @author lwz
     */
    protected function getCreateModelName(): string
    {
        return 'Controllers';
    }

    /**
     * 获取迁移文件的路径
     * @return string
     * @author lwz
     */
    protected function getMigrationPath()
    {
        $serInput = $this->getServiceInput();
        if ($serDir = $this->getServiceDirName()) {
            $serInput = $serDir . '/' . $serInput;
        }
        return $this->laravel['path'] . '/' . $serInput . '/' . $this->input->getOption('path') . '/Database/' . 'migrations';
    }
}