<?php declare(strict_types=1);
namespace Uma\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Persisters\Entity\EntityPersister;
use Uma\Domain\Model\Actor;
use Uma\Domain\Model\ActorRepository as IActorRepository;

/**
 * Doctrine implementation of the ActorRepository interface.
 *
 * @package Uma\Infrastructure\Persistence\Doctrine
 */
class ActorRepository implements IActorRepository
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * ActorRepository constructor.
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
    public function showByName(string $name): ?Actor
    {
        $persister = $this->getEntityPersister();
        $criteria = ['name' => $name];
        /** @var Actor|null $entity */
        $entity = $persister->load($criteria, null, null, [], null, 1);
        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function add(Actor $actor)
    {
        $this->entityManager->persist($actor);
    }

    /**
     * Retrieves the entity persister for Actor entities.
     *
     * @return EntityPersister
     */
    private function getEntityPersister(): EntityPersister
    {
        return $this->entityManager
                    ->getUnitOfWork()
                    ->getEntityPersister(Actor::class);
    }
}
