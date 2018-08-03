<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018/8/2 13:17
 */

namespace api\tool\lib;

/**
 * 数组操作类
 * @desc 支持任意层级子元素的增删改查
 * @package library\logic
 */
class ArrayTool implements \ArrayAccess
{
    public static function instance($array = [], $separator = '.')
    {
        return new self($array, $separator);
    }

    private $array;

    private $separator;

    public function __construct($array, $separator = '.')
    {
        $this->array     = $array;
        $this->separator = $separator;
    }

    /**
     * 设置任意层级子元素
     * @param string|array|int $key
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->set($k, $v);
            }
        } else {
            if (false === strpos($key, $this->separator)) {
                $this->array[$key] = $value;
            } else {
                $keyArray    = explode($this->separator, $key);
                $this->array = $this->recurArrayChange($this->array, $keyArray, $value);
            }
        }
        return $this;
    }

    /**
     * 获取任意层级子元素
     * @param null|string|int $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->array;
        }

        if (false === strpos($key, $this->separator)) {
            return isset($this->array[$key]) ? $this->array[$key] : $default;
        }

        $keyArray = explode($this->separator, $key);
        $tmp      = $this->array;
        foreach ($keyArray as $k) {
            if (isset($tmp[$k])) {
                $tmp = $tmp[$k];
            } else {
                $tmp = $default;
                break;
            }
        }
        return $tmp;
    }

    /**
     * 删除任意层级子元素
     * @param string|array|int $key
     * @return $this
     */
    public function delete($key)
    {
        if (is_array($key)) {
            foreach ($key as $k) {
                $this->set($k, null);
            }
        } else {
            $this->set($key, null);
        }
        return $this;
    }

    /**
     * 获取某一节点下的子元素key列表
     * @param $key
     * @return array
     */
    public function getChildKeyList($key)
    {
        $child = $this->get($key);
        $list  = [];
        $n     = 0;
        foreach ($child as $k => $v) {
            $list[$n++] = $k;
        }
        return $list;
    }

    /**
     * 递归遍历
     * @param array $array
     * @param array $keyArray
     * @param mixed $value
     * @return array
     */
    private function recurArrayChange($array, $keyArray, $value = null)
    {
        $key0 = $keyArray[0];
        if (is_array($array) && isset($keyArray[1])) {
            unset($keyArray[0]);
            $keyArray = array_values($keyArray);
            if (!isset($array[$key0])) {
                $array[$key0] = [];
            }
            $array[$key0] = $this->recurArrayChange($array[$key0], $keyArray, $value);
        } else {
            if (is_null($value)) {
                unset($array[$key0]);
            } else {
                $array[$key0] = $value;
            }
        }
        return $array;
    }

    /**
     * isset($array[$key])
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return !is_null($this->get($offset));
    }

    /**
     * $array[$key]
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * $array[$key] = $value
     * @param mixed $offset
     * @param mixed $value
     * @return $this
     */
    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

    /**
     * unset($array[$key])
     * @param mixed $offset
     * @return $this
     */
    public function offsetUnset($offset)
    {
        return $this->delete($offset);
    }
}