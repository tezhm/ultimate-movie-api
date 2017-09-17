<?php declare(strict_types=1);
namespace Uma\Infrastructure\Persistence\Doctrine;

use Uma\DatabaseTransactions;
use Uma\Domain\Model\Movie;
use Uma\LumenTest;

/**
 * Component tests for the doctrine MovieRepository implementation.
 *
 * @package Uma\Domain\Model;
 */
class MovieRepositoryTest extends LumenTest
{
    const FIXTURE_DIR = __DIR__ . '/Fixtures/Movie/';

    use DatabaseTransactions;

    /** @var MovieRepository */
    private $testClass;

    public function setUp()
    {
        parent::setUp();
        $this->testClass = $this->app->make(MovieRepository::class);
    }

    public function testShowByName()
    {
        /** @var Movie[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Movies.yml');
        $this->seedMovies($fixtures);

        $result = $this->testClass->showByName($fixtures['Movie2']->getName());

        $this->assertEquals($fixtures['Movie2'], $result);
    }

    public function testIndex()
    {
        /** @var Movie[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Movies.yml');
        $this->seedMovies($fixtures);

        $result = $this->testClass->index();

        $expected = [
            $fixtures['Movie1'],
            $fixtures['Movie2'],
            $fixtures['Movie3'],
        ];
        $this->assertEquals($expected, $result);
    }

    public function testAdd()
    {
        /** @var Movie[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Movies.yml');

        $this->testClass->add($fixtures['Movie3']);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $result = $this->testClass->showByName($fixtures['Movie3']->getName());
        $this->assertEquals($fixtures['Movie3'], $result);
    }

    private function seedMovies(array $fixtures)
    {
        $this->entityManager->persist($fixtures['Movie1']);
        $this->entityManager->persist($fixtures['Movie2']);
        $this->entityManager->persist($fixtures['Movie3']);
        $this->entityManager->flush();
    }
}
