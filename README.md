### 使用步骤
1. 安装
   ```shell
   composer require lwz/laravel-extend-artisan --dev
   ```
2. 注册服务提供者 在 config/app.php 注册 ServiceProvider(Laravel 5.5 + 无需手动注册)
   ```php
   'providers' => [
        // ...
        Lwz\LaravelExtend\Artisan\ArtisanServiceProvider::class,
    ],
   ```

3. 创建模板文件（如不创建，使用默认模板）

   ```shell
   php artisan vendor:publish --provider="Lwz\LaravelExtend\Artisan\ArtisanServiceProvider"
   ```

### 命名说明：

```shell
# 创建服务基本框架
php artisan ext-make:micro 服务名 功能名 
# 创建控制器
php artisan ext-make:controller 服务名 控制器名称
# 创建模型
php artisan ext-make:model 服务名 模型名称
# 创建迁移文件
php artisan ext-make:migration 服务名 迁移文件名
# 执行迁移文件
php artisan ext-migrate 服务名
```

