<?php declare(strict_types=1);
namespace Uma\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Persisters\Entity\EntityPersister;
use Uma\Domain\Model\Movie;
use Uma\Domain\Model\MovieRepository as IMovieRepository;

/**
 * Doctrine implementation of the MovieRepository interface.
 *
 * @package Uma\Infrastructure\Persistence\Doctrine
 */
class MovieRepository implements IMovieRepository
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * MovieRepository constructor.
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
    public function showByName(string $name): ?Movie
    {
        $persister = $this->getEntityPersister();
        $criteria = ['name' => $name];
        /** @var Movie|null $entity */
        $entity = $persister->load($criteria, null, null, [], null, 1);
        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function index(): array
    {
        $persister = $this->getEntityPersister();
        return $persister->loadAll();
    }

    /**
     * {@inheritdoc}
     */
    public function add(Movie $movie)
    {
        $this->entityManager->persist($movie);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Movie $movie)
    {
        $this->entityManager->remove($movie);
    }

    /**
     * Retrieves the entity persister for Movie entities.
     *
     * @return EntityPersister
     */
    private function getEntityPersister(): EntityPersister
    {
        return $this->entityManager
                    ->getUnitOfWork()
                    ->getEntityPersister(Movie::class);
    }
}
