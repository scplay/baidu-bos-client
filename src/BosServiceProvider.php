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

    protected $defer = true;

    /**
     * 如果设置 $defer 要在这里返回 register bind 的 name 数组
     * 如果之前没设置 defer 又改成了 defer 要先 php artisan clear-compiled
     * 查看 bootstrap -> cache -> services -> defer 数组中看有没有
     * Get the services provided by the provider
     *
     * @return array
     */
    public function provides()
    {
        return [BosInterface::class];
    }

    /**
     * 在容器中注册绑定。
     * register()有兩個功能 :
     *  手动 register 一個字符串 service provider。
     *  手动将一个 interface bind到指定的 class。
     * @return void
     */
    public function register()
    {
        $app = $this->app;

        $app->singleton( BosInterface::class ,function(){
            return new Bos( config('bos.config') , config('bos.bucket') );
        });
    }


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
            __DIR__.'/../src/config/bos.php' => config_path('bos.php'),
            
        ], 
        
         // 如指定 tag 名
         // 可以使用 php artisan vendor:publish --tag="bos-config" 来单独复制 加上 --force 可强制覆盖原有文件
         // 也可以使用 php artisan vendor:publish --provider="ZeonWang\BaiduBosClient\BosServiceProvider" 
         'bos-config'
        );

    }



}
