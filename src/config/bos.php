<?php
/**
 * BOS 的配置信息
 * 设置BosClient的Access Key ID、Secret Access Key和ENDPOINT
 * User: zeonwang
 * Date: 2017/3/16
 * Time: 9:10
 */

return [

    'config' => [
        'credentials' => [
            'ak' => env("BOS_CREDENTIAL_AK"),
            'sk' => env("BOS_CREDENTIAL_SK"),
        ],

        'endpoint' => env("BOS_ENDPOINT"),
    ],

    'bucket' => env("BOS_BUCKET")

];