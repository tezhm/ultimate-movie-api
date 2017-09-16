<?php declare(strict_types=1);
namespace Uma\Domain\Model;

use Uma\Domain\Exceptions\DomainException;

/**
 * A style/category of movies.
 *
 * @package Uma\Domain\Model
 */
class Genre extends PersistentId
{
    /** @var string */
    private $name;
    /** @var Movie[] */
    private $movies = [];
    /** @var Actor[] */
    private $actors = [];

    /**
     * Genre constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->setName($name);
    }

    /**
     * Name of the genre.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Movies within this genre.
     *
     * @return array
     */
    public function getMovies(): array
    {
        return $this->movies;
    }

    /**
     * Actors within this genre.
     *
     * @return array
     */
    public function getActors(): array
    {
        return $this->actors;
    }

    /**
     * Validates that the name is between 1 and 255 characters.
     *
     * @param string $name
     */
    public function setName(string $name)
    {
        $length = strlen($name);

        if ($length < 1 || $length > 255)
        {
            throw new DomainException('Genre name invalid');
        }

        $this->name = $name;
    }

    /**
     * Attempts to add the movie to this genre.
     *
     * @param Movie $movie
     */
    public function addMovie(Movie $movie)
    {
        if (count($this->searchForMovie($movie)) > 0)
        {
            throw new DomainException('Movie already within genre');
        }

        $this->movies[] = $movie;
    }

    /**
     * Attempts to add the actor to this genre.
     *
     * @param Actor $actor
     */
    public function addActor(Actor $actor)
    {
        if (count($this->searchForActor($actor)) > 0)
        {
            throw new DomainException('Actor already within genre');
        }

        $this->actors[] = $actor;
    }

    /**
     * Attempts to remove the movie from this genre.
     *
     * @param Movie $movie
     */
    public function removeMovie(Movie $movie)
    {
        $search = $this->searchForMovie($movie);

        if (count($search) === 0)
        {
            throw new DomainException('Movie not within genre');
        }

        reset($search);
        unset($this->movies[key($search)]);
    }

    /**
     * Attempts to remove the actor from this genre.
     *
     * @param Actor $actor
     */
    public function removeActor(Actor $actor)
    {
        $search = $this->searchForActor($actor);

        if (count($search) === 0)
        {
            throw new DomainException('Actor not within genre');
        }

        reset($search);
        unset($this->actors[key($search)]);
    }

    /**
     * Returns whether the given actor currently exists within this genre.
     *
     * @param Actor $actor
     * @return array
     */
    private function searchForActor(Actor $actor): array
    {
        $predicate = function(Actor $current) use($actor)
        {
            return ($current->getName() === $actor->getName());
        };
        return array_filter($this->getActors(), $predicate);
    }

    /**
     * Returns whether the given actor currently exists within this genre.
     *
     * @param Movie $movie
     * @return array
     */
    private function searchForMovie(Movie $movie): array
    {
        $predicate = function(Movie $current) use($movie)
        {
            return ($current->getName() === $movie->getName());
        };
        return array_filter($this->getMovies(), $predicate);
    }
}
