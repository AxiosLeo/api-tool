<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018/7/2 17:16
 */

namespace api\tool;

use api\tool\lib\HttpHelper;
use api\tool\lib\HttpResponse;

/**
 * Class Http
 * @package api\tool
 * @method $this setHeader($header_name, $header_content = '') static
 * @method $this setParam($key, $value = null) static
 * @method mixed getParam() static
 * @method $this setOption($key, $value = '') static
 * @method mixed getOption() static
 * @method $this setMethod($method) static
 * @method $this setDomain($domain) static
 * @method $this setFormat($format) static
 * @method HttpResponse curl($path = '', $data = []) static
 */
class Http
{
    private static $instance;

    /**
     * @param array $options
     * @return HttpHelper
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new HttpHelper($options);
        }
        return self::$instance;
    }

    /**
     * @return void
     */
    public static function clear()
    {
        self::$instance = null;
    }

    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([self::instance(), $name], $arguments);
    }
}