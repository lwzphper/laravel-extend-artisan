### 使用步骤

1. 注册服务提供者 在 config/app.php 注册 ServiceProvider(Laravel 5.5 + 无需手动注册)
   ```php
   'providers' => [
        // ...
        Lwz\LaravelExtend\Artisan\ArtisanServiceProvider::class,
    ],
   ```

2. 创建模板文件（如不创建，使用默认模板）

   ```shell
   php artisan vendor:publish --provider="Lwz\LaravelExtend\Artisan\ArtisanServiceProvider"
   ```

### 命名说明：

+ 创建控制器：php artisan ext-make:controller 控制路径
+ 创建服务文件：php artisan ext-make:micro 服务名/项目名
