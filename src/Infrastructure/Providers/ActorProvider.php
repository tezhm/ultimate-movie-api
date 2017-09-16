<?php declare(strict_types=1);
namespace Uma\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Uma\Domain\Model\ActorRepository as IActorRepository;
use Uma\Infrastructure\Persistence\Doctrine\ActorRepository;

class ActorProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->app->bind(IActorRepository::class, function ()
        {
            return $this->app->make(ActorRepository::class);
        });
    }
}
