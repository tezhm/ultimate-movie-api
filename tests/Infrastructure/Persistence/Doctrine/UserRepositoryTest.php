<?php declare(strict_types=1);
namespace Uma\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Laravel\Lumen\Testing\TestCase;
use Nelmio\Alice\Fixtures\Loader;
use Uma\Domain\Model\DomainRegistry;
use Uma\Domain\Model\User;

/**
 * Component tests for the doctrine UserRepository implementation.
 *
 * @package Uma\Domain\Model;
 */
class UserRepositoryTest extends TestCase
{
    const FIXTURE_DIR = __DIR__ . '/Fixtures/User/';

    /** @var Loader */
    private $alice;
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var UserRepository */
    private $testClass;

    public function setUp()
    {
        parent::setUp();
        $this->alice = new Loader('en_US');
        $this->entityManager = $this->app->make(EntityManagerInterface::class);
        $this->testClass = $this->app->make(UserRepository::class);
        $this->entityManager->beginTransaction();
        DomainRegistry::setContainer($this->app);
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->entityManager->rollback();
    }

    public function testShowByName()
    {
        /** @var User[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Users.yml');
        $this->seedUsers($fixtures);

        $result = $this->testClass->showByUsername($fixtures['User2']->getAuthIdentifier());

        $this->assertEquals($result, $fixtures['User2']);
    }

    public function testAdd()
    {
        /** @var User[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Users.yml');

        $this->testClass->add($fixtures['User3']);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $result = $this->testClass->showByUsername($fixtures['User3']->getAuthIdentifier());
        $this->assertEquals($result, $fixtures['User3']);
    }

    public function createApplication()
    {
        $app = require __DIR__ . '/../../../../bootstrap/app.php';
        return $app;
    }

    private function seedUsers(array $fixtures)
    {
        $this->entityManager->persist($fixtures['User1']);
        $this->entityManager->persist($fixtures['User2']);
        $this->entityManager->persist($fixtures['User3']);
        $this->entityManager->flush();
    }
}
