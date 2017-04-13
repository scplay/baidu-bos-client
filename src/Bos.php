<?php
/**
 * 代理BOS
 * Created by PhpStorm.
 * User: zjx
 * Date: 2016/7/14
 * Time: 14:58
 */

namespace ZeonWang\BaiduBosClient;

use BaiduBce\Auth\BceV1Signer;
use BaiduBce\Http\HttpHeaders;
use BaiduBce\Http\HttpMethod;
use BaiduBce\Services\Bos\BosClient;
use BaiduBce\Services\Bos\BosOptions;
use BaiduBce\Auth\SignOptions;

require_once 'BaiduBce.phar';

class Bos implements BosInterface
{
    public $client;

    public $bucket;

    protected $signer;

    public $sign_array;

    protected $config;

    protected $default_acl = [ // accessControlList
//          "id" => "optional",
        "accessControlList" => [[
//            "eid"=>"optional",
            "service"=>"bce:bos",
            "region"=>"*",
            "effect"=> "Allow",
            "resource"=>  ["*/*"],
            "permission"=> ["READ","WRITE"]
        ]]
    ];

    public function __construct($config, $bucket)
    {
        $this->config = $config;

        $this->client = new BosClient($this->config);

        $this->bucket = $bucket;
        
        $this->default_acl['accessControlList']['resource'] = [$this->bucket.'/*'];

        $this->signer = new BceV1Signer();

        $this->sign_array = [
            BosOptions::SIGN_OPTIONS => [
                SignOptions::TIMESTAMP => new \DateTime(),
                SignOptions::EXPIRATION_IN_SECONDS => 300,
            ]
        ];
    }

    public function getSessionToken( $acl = [] )
    {
        $entity_array =  $acl ?: $this->default_acl;
        
        $request_message = [
            'path' => '/v1/sessionToken',
            'headers' => [
                'Content-type' => 'application/json',
                'Host' => 'sts.bj.baidubce.com'
            ],
            'entity' => json_encode($entity_array) // $data_string
        ];
        
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_message['headers']['Host'].$request_message['path']);
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置是否返回响应 header
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: '.$this->generateAuthStr([],$request_message),
            'Content-type: '.$request_message['headers']['Content-type'],
            'Host: '.$request_message['headers']['Host']
        ]);//设置请求 header 字段
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_message['entity']);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        
        
        $res = curl_exec($ch);
        curl_close($ch);
        
        return $res;
        
    }
    
    public function getJsonpToken( $acl = [] )
    {
        $res_obj = json_decode($this->getSessionToken($acl));
        
        $format_json = [
            'AccessKeyId' => $res_obj->accessKeyId,
            'SecretAccessKey' => $res_obj->secretAccessKey,
            'SessionToken' => $res_obj->sessionToken,
            'Expiration' => $res_obj->expiration
        ];
        
        return json_encode($format_json);
    }
    
    /**
     * @param array $options
     * @param $request_message
     * @return mixed
     */
    public function generateAuthStr($options = array(), $request_message)
    {
        list(
            $headers,
            $params,
            $signOptions
            ) = $this->parseOptions(
            $options,
            BosOptions::HEADERS,
            BosOptions::PARAMS,
            BosOptions::SIGN_OPTIONS
        );
        
        if(is_null($params)) {
            $params = array();
        }
        if(is_null($headers)) {
            $headers = array();
        }
        
        $path = $request_message['path'];
        $headers[HttpHeaders::HOST] = $request_message['headers']['Host'];
        $headers[HttpHeaders::CONTENT_TYPE] = $request_message['headers']['Content-type'];
        $headers[HttpHeaders::CONTENT_LENGTH] = strlen($request_message['entity']);
        
        
        $auth = $this->signer->sign(
            $this->config['credentials'],
            HttpMethod::POST,
            $path,
            $headers,
            $params,
            $signOptions
        );
        
        return $auth; // 'authorization'
        
    }
    
    protected function parseOptions(array $options) {
        return $this->doParseOptions(
            $options,
            array_slice(func_get_args(), 1),
            false
        );
    }
    
    private function doParseOptions(
        array $options,
        array $args,
        $allowExtraOptions
    ) {
        $result = array();
        foreach ($args as $arg) {
            if (isset($options[$arg])) {
                $result[] = $options[$arg];
                unset($options[$arg]);
            } else {
                $result[] = null;
            }
        }
        if (!$allowExtraOptions && count($options) > 0) {
            throw new \InvalidArgumentException(
                'Unexpected options:' . implode(',', array_keys($options))
                . ' Acceptable options are:' . implode(',', $args)
            );
        }
        return $result;
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
