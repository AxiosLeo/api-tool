<?php

namespace api\tool;

require_once __DIR__ . '/base.php';
$options = [
    'http_errors'     => false,
    'connect_timeout' => 30,
    'read_timeout'    => 80,
    'urlencode'       => 1,
    'format'          => 'array',  //array|json|xml
];

$response = Http::instance($options)
    ->setDomain('http://example.com/')
    ->setMethod('GET')
    ->curl();

//get all data
dump($response->getContent());
// or $response->getData();

Http::clear(); // clear instance
