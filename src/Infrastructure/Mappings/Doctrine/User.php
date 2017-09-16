<?php declare(strict_types=1);
namespace Uma\Infrastructure\Mappings\Doctrine;

use LaravelDoctrine\Fluent\EntityMapping;
use LaravelDoctrine\Fluent\Fluent;
use Uma\Domain\Model\User as UserEntity;

/**
 * Doctrine mappings for User entity
 *
 * @package Uma\Infrastructure\Mappings\Doctrine
 */
class User extends EntityMapping
{
    /**
     * {@inheritdoc}
     */
    public function mapFor()
    {
        return UserEntity::class;
    }

    /**
     * {@inheritdoc}
     */
    public function map(Fluent $builder)
    {
        // Locals
        $builder->string('username')
                ->length(255);
        $builder->string('password')
                ->length(255);
        $builder->string('apiToken')
                ->nullable(true);

        // Indexes
        $builder->unique('username');
    }
}
