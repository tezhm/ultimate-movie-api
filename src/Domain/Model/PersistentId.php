<?php declare(strict_types=1);
namespace Uma\Domain\Model;

/**
 * Base class which provides primary key for persistence.
 *
 * @package Uma\Domain\Model
 */
abstract class PersistentId
{
    /** @var int */
    private $id;

    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }
}
