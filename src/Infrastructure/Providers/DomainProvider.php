<?php declare(strict_types=1);
namespace Uma\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Uma\Domain\Model\DomainRegistry;

class DomainProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        DomainRegistry::setContainer($this->app);
    }
}
