<?php declare(strict_types=1);
namespace Uma\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Laravel\Lumen\Testing\TestCase;
use Nelmio\Alice\Fixtures\Loader;
use Uma\Domain\Model\Movie;

/**
 * Component tests for the doctrine MovieRepository implementation.
 *
 * @package Uma\Domain\Model;
 */
class MovieRepositoryTest extends TestCase
{
    const FIXTURE_DIR = __DIR__ . '/Fixtures/Movie/';

    /** @var Loader */
    private $alice;
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var MovieRepository */
    private $testClass;

    public function setUp()
    {
        parent::setUp();
        $this->alice = new Loader('en_US');
        $this->entityManager = $this->app->make(EntityManagerInterface::class);
        $this->testClass = $this->app->make(MovieRepository::class);
        $this->entityManager->beginTransaction();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->entityManager->rollback();
    }

    public function testShowByName()
    {
        /** @var Movie[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Movies.yml');
        $this->seedMovies($fixtures);

        $result = $this->testClass->showByName($fixtures['Movie2']->getName());

        $this->assertEquals($result, $fixtures['Movie2']);
    }

    public function testAdd()
    {
        /** @var Movie[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Movies.yml');

        $this->testClass->add($fixtures['Movie3']);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $result = $this->testClass->showByName($fixtures['Movie3']->getName());
        $this->assertEquals($result, $fixtures['Movie3']);
    }

    public function createApplication()
    {
        $app = require __DIR__ . '/../../../../bootstrap/app.php';
        return $app;
    }

    private function seedMovies(array $fixtures)
    {
        $this->entityManager->persist($fixtures['Movie1']);
        $this->entityManager->persist($fixtures['Movie2']);
        $this->entityManager->persist($fixtures['Movie3']);
        $this->entityManager->flush();
    }
}
