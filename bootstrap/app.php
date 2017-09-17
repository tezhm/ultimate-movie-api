<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__ . '/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    realpath(__DIR__ . '/../')
);

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    Uma\Infrastructure\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    Uma\Infrastructure\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific Routes.
|
*/

$app->routeMiddleware([
    'auth'     => Uma\Infrastructure\Http\Middleware\Authenticate::class,
    'throttle' => GrahamCampbell\Throttle\Http\Middleware\ThrottleMiddleware::class,
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(GrahamCampbell\Throttle\ThrottleServiceProvider::class);
$app->register(Illuminate\Cookie\CookieServiceProvider::class);
$app->register(Illuminate\Session\SessionServiceProvider::class);
$app->register(LaravelDoctrine\Migrations\MigrationsServiceProvider::class);
$app->register(LaravelDoctrine\ORM\DoctrineServiceProvider::class);

$app->register(Uma\Infrastructure\Providers\ActorProvider::class);
$app->register(Uma\Infrastructure\Providers\ConsoleProvider::class);
$app->register(Uma\Infrastructure\Providers\DomainProvider::class);
$app->register(Uma\Infrastructure\Providers\GenreProvider::class);
$app->register(Uma\Infrastructure\Providers\MovieProvider::class);
$app->register(Uma\Infrastructure\Providers\UserProvider::class);

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the Routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group([
    'namespace' => 'Uma\Infrastructure\Http\Controllers',
], function ($router) {
    require __DIR__ . '/../src/Infrastructure/Routes/web.php';
});

/*
|--------------------------------------------------------------------------
| Load Configuration files for lumen
|--------------------------------------------------------------------------
|
| Need to load up the custom configuration files
|
*/
$app->configure('auth');
$app->configure('database');
$app->configure('doctrine');
$app->configure('migrations');
$app->configure('session');
$app->configure('throttle');

return $app;
