<?php

namespace api\tool;

require_once __DIR__ . '/../vendor/autoload.php';

$options = [
    'http_errors'     => false,
    'connect_timeout' => 30,
    'read_timeout'    => 80,
    'urlencode'       => 1,
    'format'          => 'array',  //array|json|xml
];

$http     = new Http($options);
$response = $http->setDomain('http://example.com/')
    ->send('', 'GET');

//get all data
dump($response->content);
// or $response->getData();
