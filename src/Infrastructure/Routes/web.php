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
$router->post('user/add/favourite', ['uses' => 'User\UserController@addFavourite', 'middleware' => ['auth']]);
$router->post('user/remove/favourite', ['uses' => 'User\UserController@removeFavourite', 'middleware' => ['auth']]);
$router->get('user/show', ['uses' => 'User\UserController@show', 'middleware' => ['auth']]);

// Actor
$router->post('actor/create', ['uses' => 'Actor\ActorController@create', 'middleware' => ['auth']]);
$router->post('actor/remove', ['uses' => 'Actor\ActorController@remove', 'middleware' => ['auth']]);
$router->post('actor/change', ['uses' => 'Actor\ActorController@change', 'middleware' => ['auth']]);
$router->get('actor/show', ['uses' => 'Actor\ActorController@show', 'middleware' => ['auth']]);
$router->get('actor/index', ['uses' => 'Actor\ActorController@index', 'middleware' => ['auth']]);

// Genre
$router->post('genre/create', ['uses' => 'Genre\GenreController@create', 'middleware' => ['auth']]);
$router->post('genre/remove', ['uses' => 'Genre\GenreController@remove', 'middleware' => ['auth']]);
$router->post('genre/add/actor', ['uses' => 'Genre\GenreController@addActor', 'middleware' => ['auth']]);
$router->post('genre/add/movie', ['uses' => 'Genre\GenreController@addMovie', 'middleware' => ['auth']]);
$router->post('genre/remove/actor', ['uses' => 'Genre\GenreController@removeActor', 'middleware' => ['auth']]);
$router->post('genre/remove/movie', ['uses' => 'Genre\GenreController@removeMovie', 'middleware' => ['auth']]);
$router->get('genre/show', ['uses' => 'Genre\GenreController@show', 'middleware' => ['auth']]);
$router->get('genre/index', ['uses' => 'Genre\GenreController@index', 'middleware' => ['auth']]);

// Movie
$router->post('movie/create', ['uses' => 'Movie\MovieController@create', 'middleware' => ['auth']]);
$router->post('movie/remove', ['uses' => 'Movie\MovieController@remove', 'middleware' => ['auth']]);
$router->post('movie/change', ['uses' => 'Movie\MovieController@change', 'middleware' => ['auth']]);
$router->post('movie/add/actor', ['uses' => 'Movie\MovieController@addActor', 'middleware' => ['auth']]);
$router->post('movie/remove/actor', ['uses' => 'Movie\MovieController@removeActor', 'middleware' => ['auth']]);
$router->get('movie/show', ['uses' => 'Movie\MovieController@show', 'middleware' => ['auth']]);
$router->get('movie/index', ['uses' => 'Movie\MovieController@index', 'middleware' => ['auth']]);
