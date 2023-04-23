
### 一、工程目录结构

```text
├── app                                 // 应用程序目录
│   └── Bar                             // 模块目录
│       └──Controller                   // 控制器目录
│           └──Api                      // 前端相关接口
│           └──Admin                    // 后台相关接口
│       └──Config                       // 模块自定义配置目录
│       └──Console                      // Schedule 及 artisan 命令目录
│       └──Events                       // 事件目录
│       └──Listeners                    // 事件监听者
│       └──Enums                        // 枚举
│       └──Constants                    // 常量
│       └──Databases                    // 数据库迁移
│       └──Request                      // 请求验证目录
│       └──Routes                       // 路由
│       └──Interfaces                   // 接口目录
│       └──Models                       // 模型目录
│       └──Providers                    // 服务提供者
│       └──Service                      // 业务逻辑层目录
│       └──Repositories                 // 仓库目录
│       └──Rpc                          // 调用外部服务的目录
│           └──Interfaces               // rpc服务接口
│           └──Services                 // rpc服务接口实现类
│       └──...                          // 其他自定义目录
│   └── README.md                       // 模块说明
│   └── ...                             // 以后增加的其他模块目录
├── config                              // 配置文件目录
├── core                               // 核心目录
│   └──Abstracts                        // 存放抽象类目录
│   └──Console                          // 存放自定义命令目录
│   └──Exception                        // 存放异常接管处理目录
│   └──Library                          // 公共类库目录
│   └──Listener                         // 存放事件监听目录
│   └──Traits                           // 存放复用类目录
│   └──CoreController.php               // 控制器基础类
│   └──CoreModel.php                    // 模型基础类
│   └──CoreRequest.php                  // 请求基础类
│   └──Rpc                              // 调用外部服务的目录（全局共用的服务，如：用户信息、基建模块接口）
│       └──Interfaces                   // rpc服务接口
│       └──Services                     // rpc服务接口实现类
├── public                              // 外部访问目录
├── storage                             // 存储目录  
├── vendor
```

### 二、初始化项目
1. 安装
   ```shell
   composer require lwz/laravel-extend-artisan --dev
   ```
   
2. 注册服务提供者 在 config/app.php 注册 ServiceProvider**(Laravel 5.5 + 无需手动注册)**
   ```php
   'providers' => [
        // ...
        Lwz\LaravelExtend\Artisan\ArtisanServiceProvider::class,
    ],
   ```
   
3. `composer.json` 添加 Core 的目录命名空间
   
    ```text
        "autoload": {
            "psr-4": {
                "App\\": "app/",
                "Core\\": "core/",
                "Database\\Factories\\": "database/factories/",
                "Database\\Seeders\\": "database/seeders/"
            }
        },
    ```
    
    ```shell
    composer dump-autoload
    ```
    
4. 创建模板文件

   ```shell
   php artisan vendor:publish --provider="Lwz\LaravelExtend\Artisan\ArtisanServiceProvider" --force
   ```

5. 将 `config/app.php` 目录下的 `App\Providers` 调整为 `Core\Providers`

    ```php
    Core\Providers\AppServiceProvider::class,
    Core\Providers\AuthServiceProvider::class,
    // Core\Providers\BroadcastServiceProvider::class,
    Core\Providers\EventServiceProvider::class,
    Core\Providers\RouteServiceProvider::class,
    ```

5. 删除 app 目录下的子目录

### 三、新建领域示例

创建 Foo 领域的 Bar 功能

1. 创建相关文件

   ```shell
   # 创建目录结构
   php artisan ext-make:micro Foo Bar 
   # 创建 TestController 控制器
   php artisan ext-make:controller Foo Api/TestController
   # 创建表单验证类
   php artisan ext-make:request Foo TestRequest
   ```

2. 将对应模块的服务提供者注册到 `Core/Providers/AppServiceProvider.php` 中

   ```php
   protected array $providers = [
       FooServiceProvider::class,
   ];
   ```

3. 在 `app/Foo/Routes/routes.php` 中定义路由

   ```php
   Route::get('/', [\App\Foo\Controllers\Api\TestController::class, 'getList']);
   ```
   
   如果需要自定义路由文件，在 `app/Foo/Providers/FooServiceProvider.php` 服务提供者的 `$routes` 属性中定义即可

### 四、命名说明：

```shell
# 创建服务基本框架
php artisan ext-make:micro 服务名 功能名 
# 创建控制器
php artisan ext-make:controller 服务名 控制器名称
# 创建模型
php artisan ext-make:model 服务名 模型名称
# 创建迁移文件
php artisan ext-make:migration 服务名 迁移文件名
# 执行迁移文件（如果需要指定数据库引擎，可以使用 --database=xxx）
php artisan ext-migrate 服务名
# 创建请求验证类
php artisan ext-make:request 服务名 请求类名
# 创建中间件
php artisan ext-make:middleware 服务名 中间件类名
# 创建数据工厂
php artisan ext-make:factory 服务名 工厂名称
# 创建数据seeder
php artisan ext-make:seeder 服务名 名称
# 执行seed（如果需要指定数据库引擎，可以使用 --database=xxx; --class=xxx 指定seeder）
php artisan ext-db:seed 服务名
```

