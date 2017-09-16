<?php declare(strict_types=1);
namespace Uma\Domain\Model;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Hashing\Hasher;

/**
 * Provider for domain infrastructure.
 *
 * @package Uma\Domain\Model
 */
class DomainRegistry
{
    /** @var Container */
    private static $container;

    /**
     * Instantiates a Hasher implementation.
     *
     * @return Hasher
     */
    public static function hashService(): Hasher
    {
        return static::$container->make(Hasher::class);
    }

    /**
     * Set the container to instantiate from.
     *
     * @param Container $container
     */
    public static function setContainer(Container $container)
    {
        static::$container = $container;
    }
}
