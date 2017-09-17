<?php declare(strict_types=1);
namespace Uma\Domain\Model;

use DateTime;
use Nelmio\Alice\Fixtures\Loader;
use PHPUnit_Framework_TestCase;
use Uma\Domain\Exceptions\DomainException;

/**
 * Unit tests for the Actor entity.
 *
 * @package Uma\Domain\Model;
 */
class ActorTest extends PHPUnit_Framework_TestCase
{
    const FIXTURE_DIR = __DIR__ . '/Fixtures/Actor/';

    /** @var Loader */
    private $alice;

    public function setUp()
    {
        parent::setUp();
        $this->alice = new Loader('en_US');
    }

    public function testConstructor()
    {
        $testClass = new Actor('sylvestor stallion', new DateTime('yesterday'));

        $this->assertEquals('sylvestor stallion', $testClass->getName());
        $this->assertEquals(new DateTime('yesterday'), $testClass->getBirth());
        $this->assertNull($testClass->getBio());
        $this->assertNull($testClass->getImage());
    }

    public function testSetName()
    {
        /** @var Actor[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'ActorStub.yml');

        $testClass = $fixtures['Actor'];
        $testClass->setName('sandra ballsbooks');

        $this->assertEquals('sandra ballsbooks', $testClass->getName());
    }

    public function testNameTooLong()
    {
        /** @var Actor[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'ActorStub.yml');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Actor name invalid');

        $testClass = $fixtures['Actor'];
        $testClass->setName(str_repeat('a', 256));
    }

    public function testNameTooShort()
    {
        /** @var Actor[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'ActorStub.yml');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Actor name invalid');

        $testClass = $fixtures['Actor'];
        $testClass->setName('');
    }

    public function testSetBirth()
    {
        /** @var Actor[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'ActorStub.yml');

        $testClass = $fixtures['Actor'];
        $birth = new DateTime('last year');
        $testClass->setBirth($birth);

        $this->assertEquals($birth, $testClass->getBirth());
    }

    public function testBirthInFuture()
    {
        /** @var Actor[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'ActorStub.yml');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Birth must be in the past');

        $testClass = $fixtures['Actor'];
        $testClass->setBirth(new DateTime('tomorrow'));
    }

    public function testGetAge()
    {
        /** @var Actor[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'ActorStub.yml');

        $testClass = $fixtures['Actor'];
        $birth = new DateTime('last year');
        $testClass->setBirth($birth);

        $this->assertEquals(1, $testClass->getAge());
    }

    public function testSetBio()
    {
        /** @var Actor[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'ActorStub.yml');

        $testClass = $fixtures['Actor'];
        $testClass->setBio('Banging your head against a wall burns 150 calories per hour');

        $this->assertEquals('Banging your head against a wall burns 150 calories per hour', $testClass->getBio());
    }

    public function testBioTooLong()
    {
        /** @var Actor[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'ActorStub.yml');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Actor biography too long');

        $testClass = $fixtures['Actor'];
        $testClass->setBio(str_repeat('b', 3001));
    }

    public function testSetImage()
    {
        /** @var Actor[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'ActorStub.yml');

        $testClass = $fixtures['Actor'];
        $testClass->setImage('this is an image cough cough cough');

        $this->assertEquals('this is an image cough cough cough', $testClass->getImage());
    }

    public function testImageTooLong()
    {
        /** @var Actor[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'ActorStub.yml');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Actor image too large');

        $testClass = $fixtures['Actor'];
        $testClass->setImage(str_repeat('b', 512001));
    }
}
