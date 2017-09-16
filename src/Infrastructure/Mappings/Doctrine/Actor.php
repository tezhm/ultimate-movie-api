<?php declare(strict_types=1);
namespace Uma\Infrastructure\Mappings\Doctrine;

use LaravelDoctrine\Fluent\EntityMapping;
use LaravelDoctrine\Fluent\Fluent;
use Uma\Domain\Model\Actor as ActorEntity;

/**
 * Doctrine mappings for Actor entity
 *
 * @package Uma\Infrastructure\Mappings\Doctrine
 */
class Actor extends EntityMapping
{
    /**
     * {@inheritdoc}
     */
    public function mapFor()
    {
        return ActorEntity::class;
    }

    /**
     * {@inheritdoc}
     */
    public function map(Fluent $builder)
    {
        // Locals
        $builder->string('name')
                ->length(255);
        $builder->dateTime('birth');
        $builder->string('bio')
                ->length(3000)
                ->nullable(true);
        $builder->blob('image')
                ->nullable(true);

        // Indexes
        $builder->unique('name');
    }
}
