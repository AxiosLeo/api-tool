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

    public function set($key, $value = null)
    {
        if (is_array($key)) {
            $this->toList($key, $list);
            $this->list = array_merge($this->list, $list);
        } else if (is_array($value)) {
            $this->toList($value, $list, $key . ".");
            $this->list = array_merge($this->list, $list);
        } else if ($value == null) {
            unset($this->list[$key]);
        } else {
            $this->list[$key] = $value;
        }
    }

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
        $keyArray = explode(".", $key);
        $value    = $this->find($keyArray, $array);
        if (!is_null($value)) {
            return $value;
        }
        if ($default instanceof \Closure) {
            return $default($key);
        }
        return $default;
    }

    public function delete($key)
    {
        if (is_array($key) || is_object($key)) {
            throw new \InvalidArgumentException($key . " cannot be array or object.");
        }
        $this->set($key, null);
    }

    /**
     * 正序排序.
     *
     * @param        $key
     * @param string $rule
     * @param bool   $save_key
     *
     * @return $this
     */
    public function sort($key = null, $rule = '', $save_key = true)
    {
        $this->sortArray($key, $rule, 'asc', $save_key);

        return $this;
    }

    /**
     * 倒序排序.
     *
     * @param        $key
     * @param string $rule
     * @param bool   $save_key
     *
     * @return $this
     */
    public function rSort($key = null, $rule = '', $save_key = true)
    {
        $this->sortArray($key, $rule, 'desc', $save_key);

        return $this;
    }

    /**
     * 支持任意层级子元素的数组排序.
     *
     * @param mixed  $key
     * @param string $sortRule
     * @param string $order
     * @param bool   $save_key
     *
     * @return mixed
     */
    private function sortArray($key = null, $sortRule = '', $order = 'asc', $save_key = true)
    {
        $array = $this->get($key);

        if (!is_array($array)) {
            return false;
        }

        /*
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
            /*
             * $sortRule = ['book'=>"asc",'version'=>"asc"];
             */
            usort($array, function ($a, $b) use ($sortRule) {
                foreach ($sortRule as $sortKey => $order) {
                    if ($a[$sortKey] == $b[$sortKey]) {
                        continue;
                    }

                    return (('asc' != $order) ? -1 : 1) * (($a[$sortKey] < $b[$sortKey]) ? -1 : 1);
                }

                return 0;
            });
        } elseif (is_string($sortRule)) {
            if (!empty($sortRule)) {
                /*
                 * $sortRule = "book";
                 * $order = "asc";
                 */
                usort($array, function ($a, $b) use ($sortRule, $order) {
                    if ($a[$sortRule] == $b[$sortRule]) {
                        return 0;
                    }

                    return (('asc' != $order) ? -1 : 1) * (($a[$sortRule] < $b[$sortRule]) ? -1 : 1);
                });
            } else {
                if ($save_key) {
                    'asc' == $order ? asort($array) : arsort($array);
                } else {
                    usort($array, function ($a, $b) use ($order) {
                        if ($a == $b) {
                            return 0;
                        }

                        return (('asc' != $order) ? -1 : 1) * (($a < $b) ? -1 : 1);
                    });
                }
            }
        }

        is_null($key) ? $this->set($array) : $this->set($key, $array);

        return $this->get($key);
    }

    private function find($keyArray, $array)
    {
        if (is_null($keyArray)) {
            dump($keyArray);
            die();
        }
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
                $tmp = $prefix == "" ? $k : $prefix . "." . $k;
                $this->toList($v, $list, $tmp);
            } else {
                $list[$prefix . "." . $k] = $v;
            }
        }
    }

    private function toArray($list, &$array = [])
    {
        foreach ($list as $key => $value) {
            $keyArray = explode(".", $key);
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
