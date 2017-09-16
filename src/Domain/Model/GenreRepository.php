<?php declare(strict_types=1);
namespace Uma\Domain\Model;

/**
 * Provides methods for retrieving and persisting Genres.
 *
 * @package Uma\Domain\Model
 */
interface GenreRepository
{
    /**
     * Retrieve an Genre by name.
     *
     * @param string $name
     * @return Genre|null
     */
    public function showByName(string $name): ?Genre;

    /**
     * Persist an Genre.
     *
     * @param Genre $Genre
     */
    public function add(Genre $Genre);

    /**
     * Removes an Genre.
     *
     * @param Genre $genre
     */
    public function remove(Genre $genre);
}
