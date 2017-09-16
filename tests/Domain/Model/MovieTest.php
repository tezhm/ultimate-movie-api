<?php declare(strict_types=1);
namespace Uma\Domain\Model;

use Nelmio\Alice\Fixtures\Loader;
use PHPUnit_Framework_TestCase;
use Uma\Domain\Exceptions\DomainException;

/**
 * Unit tests for the Movie entity.
 *
 * @package Uma\Domain\Model;
 */
class MovieTest extends PHPUnit_Framework_TestCase
{
    const FIXTURE_DIR = __DIR__ . '/Fixtures/Movie/';

    /** @var Loader */
    private $alice;

    public function setUp()
    {
        parent::setUp();
        $this->alice = new Loader('en_US');
    }

    public function testConstruct()
    {
        $testClass = new Movie('what a name');

        $this->assertEquals('what a name', $testClass->getName());
        $this->assertEquals([], $testClass->getActors());
        $this->assertEquals(0, $testClass->getRating());
        $this->assertNull($testClass->getImage());
        $this->assertNull($testClass->getDescription());
        $this->assertNull($testClass->getGenre());
    }

    public function testNameTooSmall()
    {
        /** @var Movie[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'MovieStub.yml');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Movie name invalid');

        $testClass = $fixtures['Movie'];
        $testClass->setName('');
    }

    public function testNameTooLong()
    {
        /** @var Movie[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'MovieStub.yml');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Movie name invalid');

        $testClass = $fixtures['Movie'];
        $testClass->setName(str_repeat('a', 256));
    }

    public function testSetGenre()
    {
        /** @var Movie[]|Genre[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Genre.yml');

        $testClass = $fixtures['Movie'];
        $testClass->setGenre($fixtures['Genre']);

        $this->assertEquals($fixtures['Genre'], $testClass->getGenre());
    }

    public function testAddActor()
    {
        /** @var Movie[]|Actor[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Actors.yml');

        $testClass = $fixtures['Movie'];
        $testClass->addActor($fixtures['NewActor']);

        $expectedActors = [$fixtures['AddedActor'], $fixtures['NewActor']];
        $this->assertEquals($expectedActors, $testClass->getActors());
    }

    public function testAddActorAlreadyExists()
    {
        /** @var Movie[]|Actor[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Actors.yml');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Actor already within movie');

        $testClass = $fixtures['Movie'];
        $testClass->addActor($fixtures['AddedActor']);
    }

    public function testRemoveActor()
    {
        /** @var Movie[]|Actor[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Actors.yml');

        $testClass = $fixtures['Movie'];
        $testClass->removeActor($fixtures['AddedActor']);

        $this->assertEquals([], $testClass->getActors());
    }

    public function testRemoveActorDoesNotExist()
    {
        /** @var Movie[]|Actor[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Actors.yml');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Actor not within movie');

        $testClass = $fixtures['Movie'];
        $testClass->removeActor($fixtures['NewActor']);
    }

    public function testAddRating()
    {
        /** @var Movie[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'MovieStub.yml');

        $testClass = $fixtures['Movie'];
        $testClass->addRating('user1', 0);
        $testClass->addRating('user2', 5);

        $this->assertEquals(2.5, $testClass->getRating());
    }

    public function testUpdateExistingRating()
    {
        /** @var Movie[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'MovieStub.yml');

        $testClass = $fixtures['Movie'];
        $testClass->addRating('user1', 2);
        $testClass->addRating('user1', 2);

        $this->assertEquals(2, $testClass->getRating());
    }

    public function testRatingTooSmall()
    {
        /** @var Movie[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'MovieStub.yml');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Rating must be integer between 0 and 5 (inclusive)');

        $testClass = $fixtures['Movie'];
        $testClass->addRating('user1', -1);
    }

    public function testRatingTooLarge()
    {
        /** @var Movie[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'MovieStub.yml');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Rating must be integer between 0 and 5 (inclusive)');

        $testClass = $fixtures['Movie'];
        $testClass->addRating('user1', 6);
    }

    public function testSetDescription()
    {
        /** @var Movie[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'MovieStub.yml');

        $testClass = $fixtures['Movie'];
        $testClass->setDescription('The national animal of Scotland is the unicorn');

        $this->assertEquals('The national animal of Scotland is the unicorn', $testClass->getDescription());
    }

    public function testDescriptionTooLong()
    {
        /** @var Movie[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'MovieStub.yml');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Movie description too long');

        $testClass = $fixtures['Movie'];
        $testClass->setDescription(str_repeat('z', 3001));
    }

    public function testSetImage()
    {
        /** @var Movie[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'MovieStub.yml');

        $testClass = $fixtures['Movie'];
        $testClass->setImage('this is an image cough cough cough');

        $this->assertEquals('this is an image cough cough cough', $testClass->getImage());
    }

    public function testImageTooLong()
    {
        /** @var Movie[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'MovieStub.yml');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Movie image too large');

        $testClass = $fixtures['Movie'];
        $testClass->setImage(str_repeat('b', 512001));
    }
}
