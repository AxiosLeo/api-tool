<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018/7/3 11:54
 */
require_once __DIR__ . '/base.php';

/*
 * default json format
 * support "json|jsonp|xml|html"
 */
\api\tool\Response::instance('json')
    ->response('hello,world!');
