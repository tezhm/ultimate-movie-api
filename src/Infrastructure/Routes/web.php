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

// Authentication
$router->post('login', ['uses' => 'Auth\LoginController@login']);
$router->post('logout', ['uses' => 'Auth\LoginController@logout']);

// User
$router->post('user/create', ['uses' => 'User\UserController@create']);
$router->post('user/favourite', ['uses' => 'User\UserController@favourite', 'middleware' => ['auth']]);

// Actor
$router->post('actor/create', ['uses' => 'Actor\ActorController@create', 'middleware' => ['auth']]);
$router->post('actor/remove', ['uses' => 'Actor\ActorController@remove', 'middleware' => ['auth']]);
$router->post('actor/change', ['uses' => 'Actor\ActorController@change', 'middleware' => ['auth']]);
$router->post('actor/show', ['uses' => 'Actor\ActorController@show', 'middleware' => ['auth']]);

// Genre
$router->post('genre/create', ['uses' => 'Genre\GenreController@create', 'middleware' => ['auth']]);
$router->post('genre/remove', ['uses' => 'Genre\GenreController@remove', 'middleware' => ['auth']]);
$router->post('genre/add/actor', ['uses' => 'Genre\GenreController@addActor', 'middleware' => ['auth']]);
$router->post('genre/add/movie', ['uses' => 'Genre\GenreController@addMovie', 'middleware' => ['auth']]);
$router->post('genre/remove/actor', ['uses' => 'Genre\GenreController@removeActor', 'middleware' => ['auth']]);
$router->post('genre/remove/movie', ['uses' => 'Genre\GenreController@removeMovie', 'middleware' => ['auth']]);
$router->post('genre/show', ['uses' => 'Genre\GenreController@show', 'middleware' => ['auth']]);
