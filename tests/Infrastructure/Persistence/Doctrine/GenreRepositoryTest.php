<?php declare(strict_types=1);
namespace Uma\Infrastructure\Persistence\Doctrine;

use Uma\DatabaseTransactions;
use Uma\Domain\Model\Genre;
use Uma\LumenTest;

/**
 * Component tests for the doctrine GenreRepository implementation.
 *
 * @package Uma\Domain\Model;
 */
class GenreRepositoryTest extends LumenTest
{
    const FIXTURE_DIR = __DIR__ . '/Fixtures/Genre/';

    use DatabaseTransactions;

    /** @var GenreRepository */
    private $testClass;

    public function setUp()
    {
        parent::setUp();
        $this->testClass = $this->app->make(GenreRepository::class);
    }

    public function testShowByName()
    {
        /** @var Genre[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Genres.yml');
        $this->seedGenres($fixtures);

        $result = $this->testClass->showByName($fixtures['Genre2']->getName());

        $this->assertEquals($fixtures['Genre2'], $result);
    }

    public function testIndex()
    {
        /** @var Genre[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Genres.yml');
        $this->seedGenres($fixtures);

        $result = $this->testClass->index();

        $expected = [
            $fixtures['Genre3'],
            $fixtures['Genre1'],
            $fixtures['Genre2'],
        ];
        $this->assertEquals($expected, $result);
    }

    public function testAdd()
    {
        /** @var Genre[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Genres.yml');

        $this->testClass->add($fixtures['Genre3']);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $result = $this->testClass->showByName($fixtures['Genre3']->getName());
        $this->assertEquals($fixtures['Genre3'], $result);
    }

    private function seedGenres(array $fixtures)
    {
        $this->entityManager->persist($fixtures['Genre1']);
        $this->entityManager->persist($fixtures['Genre2']);
        $this->entityManager->persist($fixtures['Genre3']);
        $this->entityManager->flush();
    }
}
