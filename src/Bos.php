<?php
/**
 * 代理BOS
 * Created by PhpStorm.
 * User: zjx
 * Date: 2016/7/14
 * Time: 14:58
 */

namespace ZeonWang\BaiduBosClient;

use BaiduBce\Services\Bos\BosClient;
use BaiduBce\Services\Bos\BosOptions;
use BaiduBce\Auth\SignOptions;

require_once 'BaiduBce.phar';

class Bos implements BosInterface
{
    public $client;
    public $bucket;
    public $sign_array;

    public function __construct($config, $bucket)
    {
        $this->client = new BosClient($config);

        $this->bucket = $bucket;

        $this->sign_array = [
            BosOptions::SIGN_OPTIONS => [
                SignOptions::TIMESTAMP => new \DateTime(),
                SignOptions::EXPIRATION_IN_SECONDS => 300,
            ]
        ];
    }

    /**
     * Facade 会将静态调用变成 call ？ 这里就可以
     * @param $name
     * @param $arguments
     * @return null
     */
    function __call($name, $arguments)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        } elseif (method_exists($this, $name)){
            return $this->$name(...$arguments);
        }
        return null;
    }

}
