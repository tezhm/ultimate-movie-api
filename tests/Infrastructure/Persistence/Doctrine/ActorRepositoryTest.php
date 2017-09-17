<?php declare(strict_types=1);
namespace Uma\Infrastructure\Persistence\Doctrine;

use Uma\DatabaseTransactions;
use Uma\Domain\Model\Actor;
use Uma\LumenTest;

/**
 * Component tests for the doctrine ActorRepository implementation.
 *
 * @package Uma\Domain\Model;
 */
class ActorRepositoryTest extends LumenTest
{
    const FIXTURE_DIR = __DIR__ . '/Fixtures/Actor/';

    use DatabaseTransactions;

    /** @var ActorRepository */
    private $testClass;

    public function setUp()
    {
        parent::setUp();
        $this->testClass = $this->app->make(ActorRepository::class);
    }

    public function testShowByName()
    {
        /** @var Actor[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Actors.yml');
        $this->seedActors($fixtures);

        $result = $this->testClass->showByName($fixtures['Actor2']->getName());

        $this->assertEquals($fixtures['Actor2'], $result);
    }

    public function testIndex()
    {
        /** @var Actor[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Actors.yml');
        $this->seedActors($fixtures);

        $result = $this->testClass->index();

        $expected = [
            $fixtures['Actor1'],
            $fixtures['Actor2'],
            $fixtures['Actor3'],
        ];
        $this->assertEquals($expected, $result);
    }

    public function testAdd()
    {
        /** @var Actor[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Actors.yml');

        $this->testClass->add($fixtures['Actor3']);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $result = $this->testClass->showByName($fixtures['Actor3']->getName());
        $this->assertEquals($fixtures['Actor3'], $result);
    }

    private function seedActors(array $fixtures)
    {
        $this->entityManager->persist($fixtures['Actor1']);
        $this->entityManager->persist($fixtures['Actor2']);
        $this->entityManager->persist($fixtures['Actor3']);
        $this->entityManager->flush();
    }
}
