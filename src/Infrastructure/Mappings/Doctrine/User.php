<?php declare(strict_types=1);
namespace Uma\Infrastructure\Mappings\Doctrine;

use LaravelDoctrine\Fluent\EntityMapping;
use LaravelDoctrine\Fluent\Fluent;
use Uma\Domain\Model\Movie as MovieEntity;
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
        $builder->string('api_token')
                ->nullable(true);

        // Relations
        $builder->manyToMany(MovieEntity::class, 'movies')
                ->joinTable('user_movies')
                ->joinColumn('user_id', 'id')
                ->inverseKey('movie_id', 'id')
                ->fetchEager()
                ->cascadePersist();

        // Indexes
        $builder->unique('username');
        $builder->unique('api_token');
    }
}
