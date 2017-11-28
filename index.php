<?php
use \SlimC\SlimC;

require __DIR__.'/vendor/autoload.php';

$app = new SlimC(array(
    'controller.namespace' => '\\Example\\Controllers',
    'templates.path' => 'views'
));

$app->get('/', function() use ($app) {
    $url1 = $app->urlFor('ExampleController.getIndex');
    $url2 = $app->urlFor('ExampleController.getPage');
    $url3 = $app->urlFor('shortname');
    $url4 = $app->urlFor('ExampleController.getPageWithVar', array('var' => 'TEST'));
    $url5 = $app->urlFor('pageById', array('id' => 10));
    $url6 = $app->urlFor('pageById', array('id' => 'wrongid'));
    echo 'Try it out: <a href="'.$url1.'">controller root</a> | '
        .'<a href="'.$url2.'">controller page</a> | '
        .'<a href="'.$url3.'">controller page by shortname</a> | '
        .'<a href="'.$url4.'">controller page with a var</a> | '
        .'<a href="'.$url5.'">controller page with id (integer)</a> | '
        .'<a href="'.$url6.'">controller page with a wrong id</a>'
    ;
});

$app->controller(
    '/example',
    'ExampleController',
    array(
        'GET /' => 'getIndex',
        'GET /page' => 'getPage',
        'GET /page shortname' => 'getPage',
        'GET,POST /page/:var' => 'getPageWithVar',
        'GET,POST /pageid/:id pageById' => 'getPageWithVar'
    ),
    array(
        "id" => "\d+" //integer validation
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
