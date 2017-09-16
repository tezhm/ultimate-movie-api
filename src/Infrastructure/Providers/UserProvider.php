<?php declare(strict_types=1);
namespace Uma\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Uma\Domain\Model\UserRepository as IUserRepository;
use Uma\Infrastructure\Persistence\Doctrine\UserRepository;

class UserProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->app->bind(IUserRepository::class, function ()
        {
            return $this->app->make(UserRepository::class);
        });
    }
}
