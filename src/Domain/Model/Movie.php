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
    public function getGenre(): Genre
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
     * @return int
     */
    public function getRating(): int
    {
        // TODO: check empty array behaviour
        return array_sum($this->rating) / count($this->rating);
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
}
