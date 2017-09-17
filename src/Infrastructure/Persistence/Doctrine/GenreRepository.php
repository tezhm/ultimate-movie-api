<?php declare(strict_types=1);
namespace Uma\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Persisters\Entity\EntityPersister;
use Uma\Domain\Model\Genre;
use Uma\Domain\Model\GenreRepository as IGenreRepository;

/**
 * Doctrine implementation of the GenreRepository interface.
 *
 * @package Uma\Infrastructure\Persistence\Doctrine
 */
class GenreRepository implements IGenreRepository
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * GenreRepository constructor.
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
    public function showByName(string $name): ?Genre
    {
        $persister = $this->getEntityPersister();
        $criteria = ['name' => $name];
        /** @var Genre|null $entity */
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
    public function add(Genre $Genre)
    {
        $this->entityManager->persist($Genre);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Genre $genre)
    {
        $this->entityManager->remove($genre);
    }

    /**
     * Retrieves the entity persister for Genre entities.
     *
     * @return EntityPersister
     */
    private function getEntityPersister(): EntityPersister
    {
        return $this->entityManager
                    ->getUnitOfWork()
                    ->getEntityPersister(Genre::class);
    }
}
