<?php declare(strict_types=1);
namespace Uma\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Laravel\Lumen\Testing\TestCase;
use Nelmio\Alice\Fixtures\Loader;
use Uma\Domain\Model\Actor;

/**
 * Component tests for the doctrine ActorRepository implementation.
 *
 * @package Uma\Domain\Model;
 */
class ActorRepositoryTest extends TestCase
{
    const FIXTURE_DIR = __DIR__ . '/Fixtures/Actor/';

    /** @var Loader */
    private $alice;
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var ActorRepository */
    private $testClass;

    public function setUp()
    {
        parent::setUp();
        $this->alice = new Loader('en_US');
        $this->entityManager = $this->app->make(EntityManagerInterface::class);
        $this->testClass = $this->app->make(ActorRepository::class);
        $this->entityManager->beginTransaction();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->entityManager->rollback();
    }

    public function testShowByName()
    {
        /** @var Actor[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Actors.yml');
        $this->seedActors($fixtures);

        $result = $this->testClass->showByName($fixtures['Actor2']->getName());

        $this->assertEquals($result, $fixtures['Actor2']);
    }

    public function testAdd()
    {
        /** @var Actor[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Actors.yml');

        $this->testClass->add($fixtures['Actor3']);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $result = $this->testClass->showByName($fixtures['Actor3']->getName());
        $this->assertEquals($result, $fixtures['Actor3']);
    }

    public function createApplication()
    {
        $app = require __DIR__ . '/../../../../bootstrap/app.php';
        return $app;
    }

    private function seedActors(array $fixtures)
    {
        $this->entityManager->persist($fixtures['Actor1']);
        $this->entityManager->persist($fixtures['Actor2']);
        $this->entityManager->persist($fixtures['Actor3']);
        $this->entityManager->flush();
    }
}
