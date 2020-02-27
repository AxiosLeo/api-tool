<?php

namespace api\tool\lib;

class Helper
{
    /**
     * 正序排序.
     *
     * @param array  $array
     * @param string $rule
     * @param bool   $save_key
     *
     * @return $this
     */
    public static function sort($array, $rule = '', $save_key = true)
    {
        return self::sortArray($array, $rule, 'asc', $save_key);
    }

    /**
     * 倒序排序.
     *
     * @param array  $array
     * @param string $rule
     * @param bool   $save_key
     *
     * @return $this
     */
    public static function rSort($array, $rule = '', $save_key = true)
    {
        return self::sortArray($array, $rule, 'desc', $save_key);
    }

    /**
     * 支持任意层级子元素的数组排序.
     *
     * @param array  $array
     * @param string $sortRule
     * @param string $order
     * @param bool   $save_key
     *
     * @return mixed
     */
    private static function sortArray($array, $sortRule = '', $order = 'asc', $save_key = true)
    {
        if (!\is_array($array)) {
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
        if (\is_array($sortRule)) {
            // $sortRule = ['book'=>"asc",'version'=>"asc"];
            usort($array, function ($a, $b) use ($sortRule) {
                foreach ($sortRule as $sortKey => $order) {
                    if ($a[$sortKey] == $b[$sortKey]) {
                        continue;
                    }

                    return (('asc' != $order) ? -1 : 1) * (($a[$sortKey] < $b[$sortKey]) ? -1 : 1);
                }

                return 0;
            });
        } elseif (\is_string($sortRule)) {
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

        return $array;
    }
}
