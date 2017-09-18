<?php declare(strict_types=1);
namespace Uma\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JsonSerializable;
use Uma\Domain\Exceptions\DomainException;

/**
 * A style/category of movies.
 *
 * @package Uma\Domain\Model
 * @SWG\Definition(type="object", @SWG\Xml(name="Genre"))
 */
class Genre extends PersistentId implements JsonSerializable
{
    /**
     * @SWG\Property()
     * @var string
     */
    private $name;
    /**
     * @SWG\Property(type="array", @SWG\Items(type="string"))
     * @var Collection(Movie[])
     */
    private $movies;
    /**
     * @SWG\Property(type="array", @SWG\Items(type="string"))
     * @var Collection(Actor[])
     */
    private $actors;

    /**
     * Genre constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->setName($name);
        $this->movies = new ArrayCollection();
        $this->actors = new ArrayCollection();
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
     * @return Movie[]
     */
    public function getMovies(): array
    {
        return $this->movies->toArray();
    }

    /**
     * Actors within this genre.
     *
     * @return Actor[]
     */
    public function getActors(): array
    {
        $movieActors = [];

        foreach ($this->getMovies() as $movie)
        {
            $movieActors = array_merge($movieActors, array_values($movie->getActors()));
        }

        $allActors = array_merge($movieActors, $this->actors->toArray());
        return $this->getUniqueActors($allActors);
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
        if (count($this->searchForMovie($movie, $this->getMovies())) > 0)
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
        if (count($this->searchForActor($actor, $this->actors->toArray())) > 0)
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
        $search = $this->searchForMovie($movie, $this->getMovies());

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
        $search = $this->searchForActor($actor, $this->actors->toArray());

        if (count($search) === 0)
        {
            throw new DomainException('Actor not within genre');
        }

        reset($search);
        unset($this->actors[key($search)]);
    }

    /**
     * Returns array containing position of actor if exists.
     *
     * @param Actor   $actor
     * @param Actor[] $haystack
     * @return array
     */
    private function searchForActor(Actor $actor, array $haystack): array
    {
        $predicate = function(Actor $current) use($actor)
        {
            return ($current->getName() === $actor->getName());
        };
        return array_filter($haystack, $predicate);
    }

    /**
     * Returns array containing position of movie if exists.
     *
     * @param Movie   $movie
     * @param Movie[] $haystack
     * @return array
     */
    private function searchForMovie(Movie $movie, array $haystack): array
    {
        $predicate = function(Movie $current) use($movie)
        {
            return ($current->getName() === $movie->getName());
        };
        return array_filter($haystack, $predicate);
    }

    /**
     * Removes duplicate Actor values.
     *
     * @param Actor[] $actors
     * @return array
     */
    private function getUniqueActors(array $actors): array
    {
        $result = [];

        foreach ($actors as $actor)
        {
            if (array_key_exists($actor->getName(), $result))
            {
                continue;
            }

            $result[$actor->getName()] = $actor;
        }

        return array_values($result);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $movies = [];
        $actors = [];

        foreach ($this->getMovies() as $movie)
        {
            $movies[] = $movie->getName();
        }

        foreach ($this->getActors() as $actor)
        {
            $actors[] = $actor->getName();
        }

        return [
            'name'   => $this->name,
            'movies' => $movies,
            'actors' => $actors,
        ];
    }
}
