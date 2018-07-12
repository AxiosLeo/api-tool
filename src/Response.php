<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018/7/2 20:34
 */

namespace api\tool;

use api\tool\lib\Parse;
use api\tool\response\Json as JsonResponse;
use api\tool\response\Jsonp as JsonpResponse;
use api\tool\response\Xml as XmlResponse;

class Response
{
    /**
     * @var array|mixed 原始数据
     */
    protected $data;

    /**
     * @var string 当前的Content-Type
     */
    protected $contentType = 'text/html';

    /**
     * @var string 字符集
     */
    protected $charset = 'utf-8';

    /**
     * @var int http状态码
     */
    protected $code = 200;

    /**
     * @var array 输出参数
     */
    protected $options = [];

    /**
     * @var array 请求头
     */
    protected $header = [];

    /**
     * @var mixed 输出内容
     */
    protected $content = null;

    /**
     * @var array 回调结果
     */
    protected $result = [];

    /**
     * @var bool 回调数组的所以元素全部转为string类型，方便应用端获取
     */
    protected $all_to_string = true;

    /**
     * @var Response
     */
    protected static $instance = null;

    /**
     * @param string $type
     * @return Response|JsonResponse|XmlResponse|JsonpResponse
     */
    public static function instance($type = 'json')
    {
        $type = empty($type) ? 'null' : strtolower($type);

        $class = false !== strpos($type, '\\') ? $type : '\\api\\tool\\response\\' . ucfirst($type);
        if (class_exists($class)) {
            self::$instance = new $class();
        } else {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * 设置请求结果中的数组元素
     * @param $key
     * @param string $value
     * @return $this
     */
    public function setResult($key, $value = '')
    {
        $this->result[$key] = $value;
        return $this;
    }

    /**
     * 回调配置
     * @param $key
     * @param string $value
     * @return $this
     */
    public function setOptions($key, $value = '')
    {
        if (is_array($key)) {
            $this->options = array_merge($this->options, $key);
        } else {
            $this->options[$key] = $value;
        }
        return $this;
    }

    /**
     * 异常情况下的请求结果
     * @param int $code
     * @param string $msg
     * @param array $header
     */
    public function wrong($code = 500, $msg = 'unknown error', array $header = [])
    {
        $this->response([], $code, $msg, $header);
    }

    /**
     * 正常情况下的请求结果
     * @param array $data
     * @param int $code
     * @param string $msg
     * @param array $header
     */
    public function response($data = [], $code = 200, $msg = 'success', array $header = [])
    {
        $this->header($header);

        $this->setResult('code', $code)
            ->setResult('msg', $msg)
            ->setResult('time', $_SERVER['REQUEST_TIME'])
            ->setResult('data', $data);

        if ($this->all_to_string) {
            $this->result = Parse::allToString($this->result);
        }

        $this->result();
    }

    /**
     * 直接回调结果
     * @param string|array|null $data
     */
    protected function result($data = null)
    {
        if (is_null($data)) {
            $data = $this->result;
        }

        $this->send($data);

    }

    /**
     * 发送数据到客户端
     * @access public
     * @param $data
     * @return void
     * @throws \InvalidArgumentException
     */
    public function send($data = [])
    {
        $this->data($data);

        $data = $this->getContent();

        $this->contentType($this->contentType, $this->charset);
        if (!headers_sent() && !empty($this->header)) {
            // 发送状态码
            http_response_code($this->code);
            // 发送头部信息
            foreach ($this->header as $name => $val) {
                if (is_null($val)) {
                    header($name);
                } else {
                    header($name . ':' . $val);
                }
            }
        }

        echo $data;

        if (function_exists('fastcgi_finish_request')) {
            // 提高页面响应
            fastcgi_finish_request();
        }

        self::$instance = null;
    }

    /**
     * 处理数据
     * @access protected
     * @param mixed $data 要处理的数据
     * @return mixed
     */
    protected function output($data)
    {
        return $data;
    }

    /**
     * 输出的参数
     * @access public
     * @param mixed $options 输出参数
     * @return $this
     */
    public function options($options = [])
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    /**
     * 输出数据设置
     * @access public
     * @param mixed $data 输出数据
     * @return $this
     */
    protected function data($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * 设置响应头
     * @access public
     * @param string|array $name 参数名
     * @param string $value      参数值
     * @return $this
     */
    public function header($name, $value = null)
    {
        if (is_array($name)) {
            $this->header = array_merge($this->header, $name);
        } else {
            $this->header[$name] = $value;
        }
        return $this;
    }

    /**
     * 设置页面输出内容
     * @param $content
     * @return $this
     */
    public function content($content)
    {
        if (null !== $content && !is_string($content) && !is_numeric($content) && !is_callable([
                $content,
                '__toString',
            ])
        ) {
            throw new \InvalidArgumentException(sprintf('variable type error： %s', gettype($content)));
        }

        $this->content = (string)$content;

        return $this;
    }

    /**
     * 发送HTTP状态
     * @param integer $code 状态码
     * @return $this
     */
    public function code($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * LastModified
     * @param string $time
     * @return $this
     */
    public function lastModified($time)
    {
        $this->header['Last-Modified'] = $time;
        return $this;
    }

    /**
     * Expires
     * @param string $time
     * @return $this
     */
    public function expires($time)
    {
        $this->header['Expires'] = $time;
        return $this;
    }

    /**
     * ETag
     * @param string $eTag
     * @return $this
     */
    public function eTag($eTag)
    {
        $this->header['ETag'] = $eTag;
        return $this;
    }

    /**
     * 页面输出类型
     * @param string $contentType 输出类型
     * @param string $charset     输出编码
     * @return $this
     */
    public function contentType($contentType, $charset = 'utf-8')
    {
        $this->header['Content-Type'] = $contentType . '; charset=' . $charset;
        return $this;
    }

    /**
     * 获取头部信息
     * @param string $name 头部名称
     * @return mixed
     */
    public function getHeader($name = '')
    {
        if (!empty($name)) {
            return isset($this->header[$name]) ? $this->header[$name] : null;
        } else {
            return $this->header;
        }
    }

    /**
     * 获取原始数据
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * 获取输出数据
     * @return mixed
     */
    public function getContent()
    {
        if (null == $this->content) {
            $content = $this->output($this->data);

            if (null !== $content && !is_string($content) && !is_numeric($content) && !is_callable([
                    $content,
                    '__toString',
                ])
            ) {
                throw new \InvalidArgumentException(sprintf('variable type error： %s', gettype($content)));
            }

            $this->content = (string)$content;
        }
        return $this->content;
    }

    /**
     * 获取状态码
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }
}