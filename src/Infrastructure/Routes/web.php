<?php declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the Routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// User authentication
$router->post('login', ['uses' => 'Auth\LoginController@login']);
$router->post('logout', ['uses' => 'Auth\LoginController@logout']);