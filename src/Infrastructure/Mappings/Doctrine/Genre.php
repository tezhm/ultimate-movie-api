<?php declare(strict_types=1);
namespace Uma\Infrastructure\Mappings\Doctrine;

use LaravelDoctrine\Fluent\EntityMapping;
use LaravelDoctrine\Fluent\Fluent;
use Uma\Domain\Model\Genre as GenreEntity;
use Uma\Domain\Model\Movie as MovieEntity;
use Uma\Domain\Model\Actor as ActorEntity;

/**
 * Doctrine mappings for Genre entity
 *
 * @package Uma\Infrastructure\Mappings\Doctrine
 */
class Genre extends EntityMapping
{
    /**
     * {@inheritdoc}
     */
    public function mapFor()
    {
        return GenreEntity::class;
    }

    /**
     * {@inheritdoc}
     */
    public function map(Fluent $builder)
    {
        // Locals
        $builder->string('name')
                ->length(255);

        // Relations
        $builder->manyToMany(MovieEntity::class, 'movies')
                ->joinTable('genre_movies')
                ->joinColumn('genre_id', 'id')
                ->inverseKey('movie_id', 'id')
                ->fetchEager()
                ->cascadePersist();

        $builder->manyToMany(ActorEntity::class, 'actors')
                ->joinTable('genre_actors')
                ->joinColumn('genre_id', 'id')
                ->inverseKey('actor_id', 'id')
                ->fetchEager()
                ->cascadePersist();

        // Indexes
        $builder->unique('name');
    }
}
