<?php declare(strict_types=1);
namespace Uma\Infrastructure\Mappings\Doctrine;

use LaravelDoctrine\Fluent\EntityMapping;
use LaravelDoctrine\Fluent\Fluent;
use Uma\Domain\Model\Actor as ActorEntity;
use Uma\Domain\Model\Genre as GenreEntity;
use Uma\Domain\Model\Movie as MovieEntity;

/**
 * Doctrine mappings for Movie entity
 *
 * @package Uma\Infrastructure\Mappings\Doctrine
 */
class Movie extends EntityMapping
{
    /**
     * {@inheritdoc}
     */
    public function mapFor()
    {
        return MovieEntity::class;
    }

    /**
     * {@inheritdoc}
     */
    public function map(Fluent $builder)
    {
        // Locals
        $builder->string('name')
                ->length(255);
        $builder->array('characters');
        $builder->array('rating');
        $builder->string('description')
                ->length(3000)
                ->nullable(true);
        $builder->blob('image')
                ->nullable(true);

        // Relations
        $builder->manyToOne(GenreEntity::class, 'genre')
                ->nullable()
                ->fetchEager()
                ->cascadePersist();

        $builder->manyToMany(ActorEntity::class, 'actors')
                ->joinTable('movie_actors')
                ->joinColumn('movie_id', 'id')
                ->inverseKey('actor_id', 'id')
                ->fetchEager()
                ->cascadePersist();

        // Indexes
        $builder->unique('name');
    }
}
