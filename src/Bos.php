<?php
/**
 * 代理BOS
 * Created by PhpStorm.
 * User: zjx
 * Date: 2016/7/14
 * Time: 14:58
 */

namespace BaiduBosClient\Bos;

use BaiduBce\Util\Time;
use BaiduBce\Services\Bos\BosClient;
use BaiduBce\Services\Bos\BosOptions;
use BaiduBce\Auth\SignOptions;

require_once 'BaiduBce.phar';

class Bos
{
    public $bucket;
    public $client;
    public $sign_array;

    public function __construct( $bucket = null )
    {
        $this->bucket = $bucket ?: env('BOS_BUCKET');

        $this->client = new BosClient( \Config::get('bos') );

        $this->sign_array = [
            BosOptions::SIGN_OPTIONS => [
                SignOptions::TIMESTAMP=>new \DateTime(),
                SignOptions::EXPIRATION_IN_SECONDS=>300,
            ]
        ];
    }

}




?>