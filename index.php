<?php
// if you're using Composer you probably won't need all this
require dirname(__FILE__) . '/../Slim/Slim/Slim.php';
require 'SlimC.php';
require 'controllers/ExampleController.php';
\Slim\SlimC::registerAutoloader();

$app = new \Slim\SlimC(array(
    'controller.namespace' => '\\Example\\Controllers',
    'templates.path' => 'views'
));

$app->get('/', function() {
    echo 'Try it out: <a href="/example">controller root</a> | ' .
        '<a href="/example/page">controller page</a> | ' .
        '<a href="/example/page/var">controller page with a var</a>';
});

$app->controller(
    '/example',
    'ExampleController',
    array(
        'GET /' => 'getIndex',
        'GET /page' => 'getPage',
        'GET,POST /page/:var' => 'getPageWithVar'
    )
);

$app->controller(
    '/other_example',
    'ExampleController',
    array(
        'GET /' => 'getSecondIndex'
    )
);

$app->run();
