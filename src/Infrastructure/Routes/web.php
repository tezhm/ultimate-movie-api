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
$router->post('user', ['uses' => 'User\UserController@create']);
$router->put('user/favourite', ['uses' => 'User\UserController@addFavourite', 'middleware' => ['auth', 'throttle:60']]);
$router->delete('user/favourite', ['uses' => 'User\UserController@removeFavourite', 'middleware' => ['auth', 'throttle:60']]);
$router->get('user', ['uses' => 'User\UserController@show', 'middleware' => ['auth', 'throttle:60']]);

// Actor
$router->post('actor', ['uses' => 'Actor\ActorController@create', 'middleware' => ['auth', 'throttle:60']]);
$router->delete('actor', ['uses' => 'Actor\ActorController@remove', 'middleware' => ['auth', 'throttle:60']]);
$router->put('actor', ['uses' => 'Actor\ActorController@change', 'middleware' => ['auth', 'throttle:60']]);
$router->get('actor', ['uses' => 'Actor\ActorController@show', 'middleware' => ['auth', 'throttle:60']]);
$router->get('actors', ['uses' => 'Actor\ActorController@index', 'middleware' => ['auth', 'throttle:60']]);

// Genre
$router->post('genre', ['uses' => 'Genre\GenreController@create', 'middleware' => ['auth', 'throttle:60']]);
$router->delete('genre', ['uses' => 'Genre\GenreController@remove', 'middleware' => ['auth', 'throttle:60']]);
$router->put('genre/actor', ['uses' => 'Genre\GenreController@addActor', 'middleware' => ['auth', 'throttle:60']]);
$router->put('genre/movie', ['uses' => 'Genre\GenreController@addMovie', 'middleware' => ['auth', 'throttle:60']]);
$router->delete('genre/actor', ['uses' => 'Genre\GenreController@removeActor', 'middleware' => ['auth', 'throttle:60']]);
$router->delete('genre/movie', ['uses' => 'Genre\GenreController@removeMovie', 'middleware' => ['auth', 'throttle:60']]);
$router->get('genre', ['uses' => 'Genre\GenreController@show', 'middleware' => ['auth', 'throttle:60']]);
$router->get('genres', ['uses' => 'Genre\GenreController@index', 'middleware' => ['auth', 'throttle:60']]);

// Movie
$router->post('movie', ['uses' => 'Movie\MovieController@create', 'middleware' => ['auth', 'throttle:60']]);
$router->delete('movie', ['uses' => 'Movie\MovieController@remove', 'middleware' => ['auth', 'throttle:60']]);
$router->put('movie', ['uses' => 'Movie\MovieController@change', 'middleware' => ['auth', 'throttle:60']]);
$router->put('movie/actor', ['uses' => 'Movie\MovieController@addActor', 'middleware' => ['auth', 'throttle:60']]);
$router->delete('movie/actor', ['uses' => 'Movie\MovieController@removeActor', 'middleware' => ['auth', 'throttle:60']]);
$router->get('movie', ['uses' => 'Movie\MovieController@show', 'middleware' => ['auth', 'throttle:60']]);
$router->get('movies', ['uses' => 'Movie\MovieController@index', 'middleware' => ['auth', 'throttle:60']]);
