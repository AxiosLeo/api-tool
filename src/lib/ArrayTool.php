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

    public function filter($array, $except = 'number')
    {
        // $except = 'number|null|string'

        $except = explode('|', $except);

        foreach ($array as &$a) {
            if (is_numeric($a) && in_array('number', $except)) {
                continue;
            }

            if (is_string($a) && in_array('string', $except)) {
                continue;
            }

            if (is_null($a) && in_array('null', $except)) {
                continue;
            }

            if (empty($a)) {
                unset($a);
            }
        }

        return $array;
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
            $keyArray    = array_values(array_filter(explode($this->separator, $key)));
            $this->array = $this->recurArrayChange($this->array, $keyArray, $value);
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
     * 正序排序
     * @param $key
     * @param string $rule
     * @param bool $save_key
     * @return $this
     */
    public function sort($key = null, $rule = "", $save_key = true)
    {
        $this->sortArray($key, $rule, 'asc', $save_key);
        return $this;
    }

    /**
     * 倒序排序
     * @param $key
     * @param string $rule
     * @param bool $save_key
     * @return $this
     */
    public function rSort($key = null, $rule = "", $save_key = true)
    {
        $this->sortArray($key, $rule, 'desc', $save_key);
        return $this;
    }

    /**
     * 支持任意层级子元素的数组排序
     * @param mixed $key
     * @param string $sortRule
     * @param string $order
     * @param bool $save_key
     * @return mixed
     */
    private function sortArray($key = null, $sortRule = "", $order = "asc", $save_key = true)
    {
        $array = $this->get($key);

        if (!is_array($array)) {
            return false;
        }

        /**
         * $array = [
         *              ["book"=>10,"version"=>10],
         *              ["book"=>19,"version"=>30],
         *              ["book"=>10,"version"=>30],
         *              ["book"=>19,"version"=>10],
         *              ["book"=>10,"version"=>20],
         *              ["book"=>19,"version"=>20]
         *      ];
         */
        if (is_array($sortRule)) {
            /**
             * $sortRule = ['book'=>"asc",'version'=>"asc"];
             */
            usort($array, function ($a, $b) use ($sortRule) {
                foreach ($sortRule as $sortKey => $order) {
                    if ($a[$sortKey] == $b[$sortKey]) {
                        continue;
                    }
                    return (($order != 'asc') ? -1 : 1) * (($a[$sortKey] < $b[$sortKey]) ? -1 : 1);
                }
                return 0;
            });
        } else if (is_string($sortRule)) {
            if (!empty($sortRule)) {
                /**
                 * $sortRule = "book";
                 * $order = "asc";
                 */
                usort($array, function ($a, $b) use ($sortRule, $order) {
                    if ($a[$sortRule] == $b[$sortRule]) {
                        return 0;
                    }
                    return (($order != 'asc') ? -1 : 1) * (($a[$sortRule] < $b[$sortRule]) ? -1 : 1);
                });
            } else {
                if ($save_key) {
                    $order == 'asc' ? asort($array) : arsort($array);
                } else {
                    usort($array, function ($a, $b) use ($order) {
                        if ($a == $b) {
                            return 0;
                        }
                        return (($order != 'asc') ? -1 : 1) * (($a < $b) ? -1 : 1);
                    });
                }
            }
        }

        is_null($key) ? $this->array = $array : $this->set($key, $array);
        return $this->array;
    }

    /**
     * 获取某一节点下的子元素key列表
     * @param $key
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
     * 递归遍历
     * @param array $array
     * @param array $keyArray
     * @param mixed $value
     * @return array
     */
    private function recurArrayChange($array, $keyArray, $value = null)
    {
        $key0 = $keyArray[0];
        if (count($keyArray) === 1) {
            $this->changeValue($array, $key0, $value);
        } else if (is_array($array) && isset($keyArray[1])) {
            unset($keyArray[0]);
            $keyArray = array_values($keyArray);
            if (!isset($array[$key0])) {
                $array[$key0] = [];
            }
            $array[$key0] = $this->recurArrayChange($array[$key0], $keyArray, $value);
        } else {
            $this->changeValue($array, $key0, $value);
        }
        return $array;
    }

    private function changeValue(&$array, $key, $value)
    {
        if (is_null($value)) {
            unset($array[$key]);
        } else {
            $array[$key] = $value;
        }
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