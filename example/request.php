<?php

require_once __DIR__ . '/../vendor/autoload.php';

$request = new \api\tool\Request();
$param   = $request->param();

dump($param);