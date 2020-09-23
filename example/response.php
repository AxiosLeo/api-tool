<?php

require_once __DIR__ . '/../vendor/autoload.php';

$response = new \api\tool\Response();

$response->response('hello,world!');
