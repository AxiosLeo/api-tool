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
    ->setDomain('https://www.sojson.com')
    ->setMethod('GET')
    ->setParam('city', '北京')
    ->curl('open/api/weather/json.shtml');

dump($response->getData('data.yesterday.date'));

//get all data
dump($response->getContent());
// or $response->getData();

Http::clear();  // clear instance

//OR
/*
Http::instance($options);
Http::setHeader([]);
Http::setDomain("https://www.sojson.com");
Http::setMethod('GET');
Http::setParam('city','北京');
$response = Http::curl("open/api/weather/json.shtml");
*/
