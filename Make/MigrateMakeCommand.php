<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/11/23 12:16,
 * @LastEditTime: 2021/11/23 12:16
 */

namespace Lwz\LaravelExtend\Artisan\Make;


use Illuminate\Database\Console\Migrations\MigrateMakeCommand as Commad;

class MigrateMakeCommand extends Commad
{
    use CommandTrait;

    protected $signature = 'ext-make:migration {service : The service of the migration} {name : The name of the migration}
        {--create= : The table to be created}
        {--table= : The table to migrate}
        {--path= : The location where the migration file should be created}
        {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
        {--fullpath : Output the full path of the migration}';

}