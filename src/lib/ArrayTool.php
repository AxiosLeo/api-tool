<?php
/**
 * @author  : axios
 * @email   : axiosleo@foxmail.com
 * @blog    : http://hanxv.cn
 * @datetime: 2018/8/2 13:17
 */

namespace api\tool\lib;

/**
 * 数组操作类.
 *
 * @desc 支持任意层级子元素的增删改查
 */
class ArrayTool implements \ArrayAccess
{
    public static function instance($array = [], $separator = ".")
    {
        return new self($array, $separator);
    }

    private $list = [];

    private $separator = "";

    public function __construct($array = [], $separator = '.')
    {
        $this->separator = $separator;
        if (!empty($array)) {
            $this->toList($array, $this->list);
        }
    }

    /**
     * @param array|string $key
     * @param null         $value
     */
    public function set($key, $value = null)
    {
        if (is_array($key)) {
            if (!empty($key)) {
                $this->toList($key, $list);
                if (is_array($list)) {
                    $this->list = array_merge($this->list, $list);
                }
            }
        } else if (is_array($value)) {
            if (empty($value)) {
                $this->list[$key] = $value;
            } else {
                $this->toList($value, $list, $key);
                $this->list = array_merge($this->list, $list);
            }
        } else if ($value == null) {
            unset($this->list[$key]);
        } else {
            $this->list[$key] = $value;
        }
    }

    /**
     * @param null $key
     * @param null $default
     *
     * @return null|mixed
     */
    public function get($key = null, $default = null)
    {
        if (is_array($key)) {
            throw new \InvalidArgumentException('$key cannot be array');
        }
        if (is_null($key)) {
            $this->toArray($this->list, $array);
            return $array;
        }
        if (isset($this->list[$key])) {
            return $this->list[$key];
        }
        $this->toArray($this->list, $array);
        if (isset($array[$key])) {
            return $array[$key];
        }
        $keyArray = explode($this->separator, $key);
        $value    = $this->find($keyArray, $array);
        if (!is_null($value)) {
            return $value;
        }
        if ($default instanceof \Closure) {
            return $default($key);
        }
        return $default;
    }

    /**
     * @param      $key
     * @param bool $deleteParentNode
     */
    public function delete($key, $deleteParentNode = true): void
    {
        if (is_array($key) || is_object($key)) {
            throw new \InvalidArgumentException($key . " cannot be array or object.");
        }
        if ($deleteParentNode) {
            $this->set($key, null);
        } else {
            $sep_pos = strrpos($key, $this->separator);
            if (false === $sep_pos) {
                $this->set($key, null);
            } else {
                $parent_key = substr($key, 0, $sep_pos);
                $child_key  = substr($key, $sep_pos + 1);
                $array      = $this->get($parent_key);
                unset($array[$child_key]);
                $this->set($parent_key, $array);
            }
        }
    }

    /**
     * @param $keyArray
     * @param $array
     *
     * @return mixed
     */
    private function find($keyArray, $array)
    {
        if (1 === count($keyArray)) {
            return isset($array[$keyArray[0]]) ? $array[$keyArray[0]] : null;
        }
        $key0 = $keyArray[0];
        unset($keyArray[0]);
        $keyArray = array_values($keyArray);
        return $this->find($keyArray, $array[$key0]);
    }

    /**
     * 获取某一节点下的子元素key列表.
     *
     * @param $key
     *
     * @return array
     */
    public function getChildKeyList($key = null)
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
     * @param string|array $except
     * @param bool         $reset_key
     *
     * @return array
     */
    public function filter($except = '', $reset_key = false)
    {
        if (empty($except)) {
            $array = array_filter($this->list);
            return $reset_key ? array_values($array) : $array;
        }
        $array = $this->list;
        foreach ($array as $k => $v) {
            if (is_numeric($v) && in_array('number', $except)) {
                continue;
            }

            if (is_string($v) && in_array('string', $except)) {
                continue;
            }

            if (is_null($v) && in_array('null', $except)) {
                continue;
            }
            if (empty($v)) {
                unset($array[$k]);
            }
        }
        return $reset_key ? array_values($array) : $array;
    }

    private function toList($array, &$list = [], $prefix = "")
    {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $tmp = $prefix == "" ? $k : $prefix . $this->separator . $k;
                $this->toList($v, $list, $tmp);
            } else if (empty($prefix)) {
                $list[$k] = $v;
            } else {
                $list[$prefix . $this->separator . $k] = $v;
            }
        }
    }

    private function toArray($list, &$array = [])
    {
        foreach ($list as $key => $value) {
            $keyArray = explode($this->separator, $key);
            $this->value($keyArray, $value, $array);
        }
    }

    private function value($key, $value, &$array)
    {
        if (!is_array($key)) {
            $array[$key] = $value;
        } else if (is_array($key) && count($key) === 1) {
            $array[$key[0]] = $value;
        } else if (is_array($key)) {
            $key0 = $key[0];
            unset($key[0]);
            $key = array_values($key);
            $this->value($key, $value, $array[$key0]);
        }
        return $array;
    }

    /**
     * isset($array[$key])
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return !is_null($this->get($offset));
    }

    /**
     * $array[$key]
     * @param mixed $offset
     *
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
     *
     * @return $this
     */
    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

    /**
     * unset($array[$key])
     *
     * @param mixed $offset
     *
     * @return $this
     */
    public function offsetUnset($offset)
    {
        return $this->set($offset, null);
    }
}
