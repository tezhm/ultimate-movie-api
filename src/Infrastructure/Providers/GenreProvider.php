<?php declare(strict_types=1);
namespace Uma\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Uma\Domain\Model\GenreRepository as IGenreRepository;
use Uma\Infrastructure\Persistence\Doctrine\GenreRepository;

class GenreProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->app->bind(IGenreRepository::class, function ()
        {
            return $this->app->make(GenreRepository::class);
        });
    }
}
