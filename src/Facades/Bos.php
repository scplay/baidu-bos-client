<?php

namespace ZeonWang\BaiduBosClient\Facades;

use Illuminate\Support\Facades\Facade;
use ZeonWang\BaiduBosClient\BosInterface;

class Bos extends Facade
{
    protected static function getFacadeAccessor()
    {
        return BosInterface::class;
    }
}
