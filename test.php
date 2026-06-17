<?php

use AidenKR\InternetAPI\InternetAPI;

require_once __DIR__ . '/vendor/autoload.php';

$response = InternetAPI::get("https://jsonplaceholder.typicode.com/posts/1");

var_dump($response);

print str_repeat("-", 10);

$second_response = InternetAPI::post("https://jsonplaceholder.typicode.com/posts/", [
    "title" => "Hello World!",
    "body" => "Hello World!",
    "userId" => 1
]);
var_dump($second_response);

print str_repeat("-", 10);

