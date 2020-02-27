<?php

//need composer install

/**
 * 数据打印或输出.
 *
 * @param $var
 * @param bool $echo
 * @param null $label
 * @param int  $flags
 *
 * @return null|string|string[]
 */
function dump($var = null, $echo = true, $label = null, $flags = ENT_SUBSTITUTE)
{
    $label = (null === $label) ? '' : rtrim($label) . ':';
    ob_start();
    var_dump($var);
    $output = ob_get_clean();
    $output = preg_replace('/]=>\n(\s+)/m', '] => ', $output);
    if (!extension_loaded('xdebug')) {
        $output = htmlspecialchars($output, $flags);
    }
    $output = '<pre>' . $label . $output . '</pre>';
    if ($echo) {
        echo $output;

        return '';
    }

    return $output;
}

require_once __DIR__ . '/../vendor/autoload.php';
