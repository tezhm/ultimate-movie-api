<?php declare(strict_types=1);
namespace Uma\Infrastructure\Mappings\Doctrine;

use LaravelDoctrine\Fluent\MappedSuperClassMapping;
use LaravelDoctrine\Fluent\Fluent;
use Uma\Domain\Model\PersistentId as PersistentIdBase;

/**
 * Doctrine mapping for PersistentId base class.
 *
 * @package Uma\Infrastructure\Mappings\Doctrine
 */
class PersistentId extends MappedSuperClassMapping
{
    /**
     * {@inheritdoc}
     */
    public function mapFor()
    {
        return PersistentIdBase::class;
    }

    /**
     * {@inheritdoc}
     */
    public function map(Fluent $builder)
    {
        $builder->increments('id');
    }
}
