<?php declare(strict_types=1);
namespace Uma\Domain\Model;

/**
 * Provides methods for retrieving and persisting Users.
 *
 * @package Uma\Domain\Model
 */
interface UserRepository
{
    /**
     * Retrieve an User by name.
     *
     * @param string $username
     * @return User|null
     */
    public function showByUsername(string $username): ?User;

    /**
     * Persist an User.
     *
     * @param User $user
     */
    public function add(User $user);

    /**
     * Removes an User.
     *
     * @param User $user
     */
    public function remove(User $user);
}
