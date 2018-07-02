<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018/7/2 17:16
 */

namespace api\tool;

use api\tool\lib\HttpHelper;

class Http extends HttpHelper
{
    private static $instance;

    public static function instance($options = []){
        if(is_null(self::$instance)){
            self::$instance = new static($options);
        }
        return self::$instance;
    }
}