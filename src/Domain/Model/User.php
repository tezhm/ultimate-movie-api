<?php declare(strict_types=1);
namespace Uma\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Illuminate\Contracts\Auth\Authenticatable;
use Uma\Domain\Exceptions\DomainException;

/**
 * Contains user information.
 *
 * @package Uma\Domain\Model
 */
class User extends PersistentId implements Authenticatable
{
    /** @var string */
    private $username;
    /** @var string */
    private $password;
    /** @var string */
    private $api_token;
    /** @var Collection(Movie[]) */
    private $movies;

    /**
     * User constructor.
     *
     * @param string $username
     * @param string $password
     */
    public function __construct(string $username, string $password)
    {
        $this->setUsername($username);
        $this->setPassword($password);
        $this->api_token = null;
        $this->movies = new ArrayCollection();
    }

    /**
     * Generates a new API token.
     */
    public function generateApiToken()
    {
        // Not cryptographically safe - would normally use a domain service to generate pseudo random values
        $this->api_token = str_random(32);
    }

    /**
     * Returns the last generated API token.
     *
     * @return string|null
     */
    public function getApiToken(): ?string
    {
        return $this->api_token;
    }

    /**
     * Returns the favourited movies of a user.
     *
     * @return Movie[]
     */
    public function getFavourites(): array
    {
        return $this->movies->toArray();
    }

    /**
     * Adds a movie as a new favourite.
     *
     * @param Movie $movie
     */
    public function addFavourite(Movie $movie)
    {
        if (count($this->searchForMovie($movie, $this->getFavourites())) > 0)
        {
            throw new DomainException('Movie already favourited');
        }

        $this->movies[] = $movie;
    }

    /**
     * Removes a movie from favourites.
     *
     * @param Movie $movie
     */
    public function removeFavourite(Movie $movie)
    {
        $search = $this->searchForMovie($movie, $this->getFavourites());

        if (count($search) === 0)
        {
            throw new DomainException('Movie not favourited');
        }

        reset($search);
        unset($this->movies[key($search)]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthIdentifierName()
    {
        return 'username';
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthIdentifier()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getRememberToken()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setRememberToken($value)
    {
        // Not implementing remember token
    }

    /**
     * {@inheritdoc}
     */
    public function getRememberTokenName()
    {
        return null;
    }

    /**
     * Validates the username is 4 to 16 characters and does not contain invalid characters.
     *
     * @param string $username
     */
    private function setUsername(string $username)
    {
        $length = strlen($username);

        if ($length < 4 || $length > 16)
        {
            throw new DomainException('User username invalid');
        }

        if (!mb_check_encoding($username, 'ASCII'))
        {
            throw new DomainException('User username invalid');
        }

        // Only allowing alphanumeric usernames for simplicity sake
        if (!ctype_alnum($username))
        {
            throw new DomainException('User username invalid');
        }

        $this->username = $username;
    }

    /**
     * Validates the password is 8 to 24 characters in size.
     *
     * @param string $password
     */
    private function setPassword(string $password)
    {
        $length = strlen($password);

        if ($length < 8 || $length > 24)
        {
            throw new DomainException('User password invalid');
        }

        $this->password = DomainRegistry::hashService()->make($password);
    }

    /**
     * Returns whether the given movie exists in the array.
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
}
