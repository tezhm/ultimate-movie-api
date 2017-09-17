<?php declare(strict_types=1);
namespace Uma\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JsonSerializable;
use Uma\Domain\Exceptions\DomainException;

/**
 * Details of a movie.
 *
 * @package Uma\Domain\Model
 */
class Movie extends PersistentId implements JsonSerializable
{
    /** @var string */
    private $name;
    /** @var Genre */
    private $genre;
    /** @var Collection(Actor[]) */
    private $actors;
    /** @var string[] */
    private $characters = [];
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
        $this->actors = new ArrayCollection();
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
        $actors = [];

        for ($i = 0; $i < count($this->characters); ++$i)
        {
            $actors[$this->characters[$i]] = $this->actors[$i];
        }

        return $actors;
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
     * @param string $character
     * @param Actor  $actor
     */
    public function addActor(string $character, Actor $actor)
    {
        $roles = $this->searchForRoles($actor);

        if (in_array($character, $roles, true))
        {
            throw new DomainException('Actor already within movie');
        }

        $this->actors[] = $actor;
        $this->characters[] = $character;
    }

    /**
     * Attempts to remove the actor from this movie.
     *
     * @param Actor $actor
     */
    public function removeActor(Actor $actor)
    {
        $search = $this->searchForRoles($actor);

        if (count($search) === 0)
        {
            throw new DomainException('Actor not within movie');
        }

        foreach (array_keys($search) as $key)
        {
            unset($this->actors[$key]);
            unset($this->characters[$key]);
        }
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
     * Returns the roles played by actor.
     *
     * @param Actor $actor
     * @return array
     */
    private function searchForRoles(Actor $actor): array
    {
        $positions = $this->searchForActor($actor, $this->actors->toArray());
        return array_intersect_key($this->characters, $positions);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $actors = [];

        foreach ($this->getActors() as $character => $actor)
        {
            $actors[$character] = $actor->getName();
        }

        return [
            'name'        => $this->getName(),
            'genre'       => ($this->genre === null) ? null : $this->genre->getName(),
            'actors'      => $actors,
            'rating'      => $this->getRating() ,
            'description' => $this->getDescription(),
            'image'       => $this->getImage(),
        ];
    }
}
