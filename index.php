<?php
use \Slim\SlimC;
require         __DIR__.'/vendor/autoload.php';
require 'controllers/ExampleController.php';
//\Slim\SlimC::registerAutoloader();

$app = new SlimC(array(
    'controller.namespace' => '\\Example\\Controllers',
    'templates.path' => 'views'
));

$app->get('/', function() use ($app) {
    $url1 = $app->urlFor('ExampleController.getIndex');
    $url2 = $app->urlFor('ExampleController.getPage');
    $url3 = $app->urlFor('ExampleController.getPageWithVar', array('var' => 'TEST'));
    echo 'Try it out: <a href="'.$url1.'">controller root</a> | ' .
        '<a href="'.$url2.'">controller page</a> | ' .
        '<a href="'.$url3.'">controller page with a var</a>';
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
