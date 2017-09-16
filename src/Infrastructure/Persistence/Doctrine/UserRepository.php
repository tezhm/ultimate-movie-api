<?php declare(strict_types=1);
namespace Uma\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Persisters\Entity\EntityPersister;
use Uma\Domain\Model\User;
use Uma\Domain\Model\UserRepository as IUserRepository;

/**
 * Doctrine implementation of the UserRepository interface.
 *
 * @package Uma\Infrastructure\Persistence\Doctrine
 */
class UserRepository implements IUserRepository
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * UserRepository constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function showByUsername(string $username): ?User
    {
        $persister = $this->getEntityPersister();
        $criteria = ['username' => $username];
        /** @var User|null $entity */
        $entity = $persister->load($criteria, null, null, [], null, 1);
        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function add(User $movie)
    {
        $this->entityManager->persist($movie);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(User $movie)
    {
        $this->entityManager->remove($movie);
    }

    /**
     * Retrieves the entity persister for User entities.
     *
     * @return EntityPersister
     */
    private function getEntityPersister(): EntityPersister
    {
        return $this->entityManager
                    ->getUnitOfWork()
                    ->getEntityPersister(User::class);
    }
}
