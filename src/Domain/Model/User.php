<?php declare(strict_types=1);
namespace Uma\Domain\Model;

/**
 * Contains user information.
 *
 * @package Uma\Domain\Model
 */
class User extends PersistentId
{
    /** @var string */
    private $username;
    /** @var string */
    private $password;
    /** @var string */
    private $apiToken;

    /**
     * User constructor.
     *
     * @param string $username
     * @param string $password
     */
    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
        $this->apiToken = null;
    }
}
