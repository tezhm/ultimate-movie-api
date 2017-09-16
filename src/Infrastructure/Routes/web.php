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

// Authentication
$router->post('login', ['uses' => 'Auth\LoginController@login']);
$router->post('logout', ['uses' => 'Auth\LoginController@logout']);

// User
$router->post('user/create', ['uses' => 'User\UserController@create']);
$router->post('user/favourite', ['uses' => 'User\UserController@favourite']);

// Actor
$router->post('actor/create', ['uses' => 'Actor\ActorController@create', 'middleware' => ['auth']]);
$router->post('actor/remove', ['uses' => 'Actor\ActorController@remove', 'middleware' => ['auth']]);
$router->post('actor/change', ['uses' => 'Actor\ActorController@change', 'middleware' => ['auth']]);
$router->post('actor/show', ['uses' => 'Actor\ActorController@show', 'middleware' => ['auth']]);
