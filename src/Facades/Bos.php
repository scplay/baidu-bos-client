<?php

namespace ZeonWang\BaiduBosClient\Facades;

use Illuminate\Support\Facades\Facade;

class Bos extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'bos';
    }
}
