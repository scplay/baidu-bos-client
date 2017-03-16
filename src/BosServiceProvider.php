<?php
/**
 * 代理BOS
 * Created by PhpStorm.
 * User: zjx
 * Date: 2016/7/14
 * Time: 14:58
 */

namespace ZeonWang\BaiduBosClient;

use Illuminate\Support\ServiceProvider;

class BosServiceProvider extends ServiceProvider
{
    /**
     * 在注册后进行服务的启动。
     * 可以使用 php artisan vendor:publish 复制到 Laravel 的 config 目录中
     * 可以使用 $endpoint = config('bos.endpoint'); 来访问到对应的 key value
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            // 从本地指定目录复制到 laravel 的 config 目录
            __DIR__.'/../config/bos.php' => config_path('bos.php'),
            // 如指定 tag 名
            // 可以使用 php artisan vendor:publish --tag=config 来单独复制 加上 --force 可强制覆盖原有文件
            'config'
        ]);
    }

    /**
     * 在容器中注册绑定。
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Bos',function(){
           return new Bos( config('bos.config') , config('bos.bucket') );
        });
    }

}




?>