<?php declare(strict_types=1);
namespace Uma\Domain\Model;

use Uma\Domain\Exceptions\DomainException;

/**
 * Details of a movie.
 *
 * @package Uma\Domain\Model
 */
class Movie extends PersistentId
{
    /** @var string */
    private $name;
    /** @var Genre */
    private $genre;
    /** @var Actor[] */
    private $actors = [];
    /** @var int[] */
    private $rating = [];
    /** @var string|null */
    private $description;
    /** @var string|null */
    private $image;

    /**
     * Movie constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->setName($name);
    }

    /**
     * Returns the name of the movie.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the genre of this movie.
     *
     * @return Genre
     */
    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    /**
     * Returns the actors within this movie.
     *
     * @return Actor[]
     */
    public function getActors(): array
    {
        return $this->actors;
    }

    /**
     * Returns the average rating.
     *
     * @return float
     */
    public function getRating(): float
    {
        $count = count($this->rating);

        if ($count === 0)
        {
            return 0;
        }

        $average = array_sum($this->rating) / count($this->rating);
        return round($average, 1);
    }

    /**
     * Short description of the movie.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * The image of the movie.
     *
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
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
            throw new DomainException('Movie name invalid');
        }

        $this->name = $name;
    }

    /**
     * Sets the genre of this movie.
     *
     * @param Genre $genre
     */
    public function setGenre(Genre $genre)
    {
        $this->genre = $genre;
    }

    /**
     * Attempts to add the actor to this movie.
     *
     * @param Actor $actor
     */
    public function addActor(Actor $actor)
    {
        if (count($this->searchForActor($actor)) > 0)
        {
            throw new DomainException('Actor already within movie');
        }

        $this->actors[] = $actor;
    }

    /**
     * Attempts to remove the actor from this movie.
     *
     * @param Actor $actor
     */
    public function removeActor(Actor $actor)
    {
        $search = $this->searchForActor($actor);

        if (count($search) === 0)
        {
            throw new DomainException('Actor not within movie');
        }

        reset($search);
        unset($this->actors[key($search)]);
    }

    /**
     * Sets the rating given by a user.
     *
     * @param string $user
     * @param int $rating
     */
    public function addRating(string $user, int $rating)
    {
        if ($rating < 0 || $rating > 5)
        {
            throw new DomainException('Rating must be integer between 0 and 5 (inclusive)');
        }

        $this->rating[$user] = $rating;
    }

    /**
     * Provides 3000 characters for description.
     *
     * @param string|null $description
     */
    public function setDescription(?string $description)
    {
        if ($description !== null && strlen($description) > 3000)
        {
            throw new DomainException('Movie description too long');
        }

        $this->description = $description;
    }

    /**
     * Allows up to ~500kB images.
     *
     * @param string|null $image
     */
    public function setImage(?string $image)
    {
        // Could run the image through an image normalisation service
        if ($image !== null && strlen($image) > 512000)
        {
            throw new DomainException('Movie image too large');
        }

        $this->image = $image;
    }

    /**
     * Returns whether the given actor currently exists within this movie.
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
}
