<?php declare(strict_types=1);
namespace Uma\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Uma\Infrastructure\Console\PingDatabase;

class ConsoleProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->commands(PingDatabase::class);
    }
}
