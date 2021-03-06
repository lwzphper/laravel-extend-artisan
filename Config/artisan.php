<?php
/**
 * @Author: laoweizhen <1149243551@qq.com>,
 * @Date: 2021/10/27 16:01,
 * @LastEditTime: 2021/10/27 16:01
 */

$serviceDirName = '';
$rootNamespace = 'App';
return [
    'package' => [
        'dir' => $serviceDirName, // 服务目录名
        'root_namespace' => $rootNamespace. '\\', // 跟命名空间
        'root_dirname' => lcfirst($rootNamespace), // 跟目录名称
        'namespace' => $rootNamespace . $serviceDirName, // 命名空间
    ],
];