<?php

namespace api\tool;

use api\tool\lib\HttpHelper;
use api\tool\lib\HttpResponse;

/**
 * Class Http.
 *
 * @method $this        setHeader($header_name, $header_content = '') static
 * @method $this        setParam($key, $value = null)                 static
 * @method mixed        getParam()                                    static
 * @method $this        setOption($key, $value = '')                  static
 * @method mixed        getOption()                                   static
 * @method $this        setMethod($method)                            static
 * @method $this        setDomain($domain)                            static
 * @method $this        setFormat($format)                            static
 * @method HttpResponse curl($path = '', $data = [])                  static
 */
class Http
{
    private static $instance;

    public static function __callStatic($name, $arguments)
    {
        return \call_user_func_array([self::instance(), $name], $arguments);
    }

    /**
     * @param array $options
     *
     * @return HttpHelper
     */
    public static function instance($options = [])
    {
        if (null === self::$instance) {
            self::$instance = new HttpHelper($options);
        }

        return self::$instance;
    }

    public static function clear()
    {
        self::$instance = null;
    }
}
