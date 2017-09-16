<?php declare(strict_types=1);
namespace Uma\Domain\Model;

/**
 * Provides methods for retrieving and persisting Movies.
 *
 * @package Uma\Domain\Model
 */
interface MovieRepository
{
    /**
     * Retrieve an Movie by name.
     *
     * @param string $name
     * @return Movie|null
     */
    public function showByName(string $name): ?Movie;

    /**
     * Persist an Movie.
     *
     * @param Movie $Movie
     */
    public function add(Movie $Movie);
}
