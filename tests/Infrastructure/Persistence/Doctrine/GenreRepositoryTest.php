<?php declare(strict_types=1);
namespace Uma\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Laravel\Lumen\Testing\TestCase;
use Nelmio\Alice\Fixtures\Loader;
use Uma\Domain\Model\Genre;

/**
 * Component tests for the doctrine GenreRepository implementation.
 *
 * @package Uma\Domain\Model;
 */
class GenreRepositoryTest extends TestCase
{
    const FIXTURE_DIR = __DIR__ . '/Fixtures/Genre/';

    /** @var Loader */
    private $alice;
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var GenreRepository */
    private $testClass;

    public function setUp()
    {
        parent::setUp();
        $this->alice = new Loader('en_US');
        $this->entityManager = $this->app->make(EntityManagerInterface::class);
        $this->testClass = $this->app->make(GenreRepository::class);
        $this->entityManager->beginTransaction();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->entityManager->rollback();
    }

    public function testShowByName()
    {
        /** @var Genre[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Genres.yml');
        $this->seedGenres($fixtures);

        $result = $this->testClass->showByName($fixtures['Genre2']->getName());

        $this->assertEquals($result, $fixtures['Genre2']);
    }

    public function testAdd()
    {
        /** @var Genre[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Genres.yml');

        $this->testClass->add($fixtures['Genre3']);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $result = $this->testClass->showByName($fixtures['Genre3']->getName());
        $this->assertEquals($result, $fixtures['Genre3']);
    }

    public function createApplication()
    {
        $app = require __DIR__ . '/../../../../bootstrap/app.php';
        return $app;
    }

    private function seedGenres(array $fixtures)
    {
        $this->entityManager->persist($fixtures['Genre1']);
        $this->entityManager->persist($fixtures['Genre2']);
        $this->entityManager->persist($fixtures['Genre3']);
        $this->entityManager->flush();
    }
}
