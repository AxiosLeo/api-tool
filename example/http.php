<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018/7/3 11:51
 */

require_once __DIR__ . '/base.php';

$response = \api\tool\Http::instance()
    ->setDomain("https://www.sojson.com")
    ->setMethod('GET')
    ->curl("open/api/weather/json.shtml",[
        "city"=>'北京'
    ]);

dump($response->getContent());