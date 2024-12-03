<?php
/**
 * 作者:郭磊
 * 邮箱:174000902@qq.com
 * 电话:15210720528
 * Git:https://github.com/guolei19850528/laravel-lmobile
 */

namespace Guolei19850528\Laravel\Lmobile;

use Illuminate\Support\ServiceProvider;


/**
 * 扩展服务提供者
 */
class ExtensionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        /**
         * 发布配置文件
         */
        $this->publishes([__DIR__ . '/../config/guolei19850528-laravel-lmobile.php' => config_path('guolei19850528-laravel-lmobile.php')], 'guolei19850528/laravel-lmobile');
    }
}
