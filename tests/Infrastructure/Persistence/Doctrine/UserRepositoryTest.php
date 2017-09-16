<?php declare(strict_types=1);
namespace Uma\Infrastructure\Persistence\Doctrine;

use Uma\DatabaseTransactions;
use Uma\Domain\Model\DomainRegistry;
use Uma\Domain\Model\User;
use Uma\LumenTest;

/**
 * Component tests for the doctrine UserRepository implementation.
 *
 * @package Uma\Domain\Model;
 */
class UserRepositoryTest extends LumenTest
{
    const FIXTURE_DIR = __DIR__ . '/Fixtures/User/';

    use DatabaseTransactions;

    /** @var UserRepository */
    private $testClass;

    public function setUp()
    {
        parent::setUp();
        $this->testClass = $this->app->make(UserRepository::class);
        DomainRegistry::setContainer($this->app);
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

    private function seedUsers(array $fixtures)
    {
        $this->entityManager->persist($fixtures['User1']);
        $this->entityManager->persist($fixtures['User2']);
        $this->entityManager->persist($fixtures['User3']);
        $this->entityManager->flush();
    }
}
