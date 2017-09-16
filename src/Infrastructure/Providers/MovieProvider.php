<?php declare(strict_types=1);
namespace Uma\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Uma\Domain\Model\MovieRepository as IMovieRepository;
use Uma\Infrastructure\Persistence\Doctrine\MovieRepository;

class MovieProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->app->bind(IMovieRepository::class, function ()
        {
            return $this->app->make(MovieRepository::class);
        });
    }
}
