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
$router->post('user/add/favourite', ['uses' => 'User\UserController@addFavourite', 'middleware' => ['auth', 'throttle:60']]);
$router->post('user/remove/favourite', ['uses' => 'User\UserController@removeFavourite', 'middleware' => ['auth', 'throttle:60']]);
$router->get('user/show', ['uses' => 'User\UserController@show', 'middleware' => ['auth', 'throttle:60']]);

// Actor
$router->post('actor', ['uses' => 'Actor\ActorController@create', 'middleware' => ['auth', 'throttle:60']]);
$router->delete('actor', ['uses' => 'Actor\ActorController@remove', 'middleware' => ['auth', 'throttle:60']]);
$router->put('actor', ['uses' => 'Actor\ActorController@change', 'middleware' => ['auth', 'throttle:60']]);
$router->get('actor/showByName', ['uses' => 'Actor\ActorController@show', 'middleware' => ['auth', 'throttle:60']]);
$router->get('actor/index', ['uses' => 'Actor\ActorController@index', 'middleware' => ['auth', 'throttle:60']]);

// Genre
$router->post('genre/create', ['uses' => 'Genre\GenreController@create', 'middleware' => ['auth', 'throttle:60']]);
$router->post('genre/remove', ['uses' => 'Genre\GenreController@remove', 'middleware' => ['auth', 'throttle:60']]);
$router->post('genre/add/actor', ['uses' => 'Genre\GenreController@addActor', 'middleware' => ['auth', 'throttle:60']]);
$router->post('genre/add/movie', ['uses' => 'Genre\GenreController@addMovie', 'middleware' => ['auth', 'throttle:60']]);
$router->post('genre/remove/actor', ['uses' => 'Genre\GenreController@removeActor', 'middleware' => ['auth', 'throttle:60']]);
$router->post('genre/remove/movie', ['uses' => 'Genre\GenreController@removeMovie', 'middleware' => ['auth', 'throttle:60']]);
$router->get('genre/show', ['uses' => 'Genre\GenreController@show', 'middleware' => ['auth', 'throttle:60']]);
$router->get('genre/index', ['uses' => 'Genre\GenreController@index', 'middleware' => ['auth', 'throttle:60']]);

// Movie
$router->post('movie/create', ['uses' => 'Movie\MovieController@create', 'middleware' => ['auth', 'throttle:60']]);
$router->post('movie/remove', ['uses' => 'Movie\MovieController@remove', 'middleware' => ['auth', 'throttle:60']]);
$router->post('movie/change', ['uses' => 'Movie\MovieController@change', 'middleware' => ['auth', 'throttle:60']]);
$router->post('movie/add/actor', ['uses' => 'Movie\MovieController@addActor', 'middleware' => ['auth', 'throttle:60']]);
$router->post('movie/remove/actor', ['uses' => 'Movie\MovieController@removeActor', 'middleware' => ['auth', 'throttle:60']]);
$router->get('movie/show', ['uses' => 'Movie\MovieController@show', 'middleware' => ['auth', 'throttle:60']]);
$router->get('movie/index', ['uses' => 'Movie\MovieController@index', 'middleware' => ['auth', 'throttle:60']]);
