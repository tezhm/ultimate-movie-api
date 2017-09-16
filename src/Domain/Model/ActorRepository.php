<?php declare(strict_types=1);
namespace Uma\Domain\Model;

/**
 * Provides methods for retrieving and persisting Actors.
 *
 * @package Uma\Domain\Model
 */
interface ActorRepository
{
    /**
     * Retrieve an actor by name.
     *
     * @param string $name
     * @return Actor|null
     */
    public function showByName(string $name): ?Actor;

    /**
     * Persist an actor.
     *
     * @param Actor $actor
     */
    public function add(Actor $actor);

    /**
     * Removes an actor.
     *
     * @param Actor $actor
     */
    public function remove(Actor $actor);
}
