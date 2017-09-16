<?php declare(strict_types=1);
namespace Uma\Domain\Model;

use Nelmio\Alice\Fixtures\Loader;
use PHPUnit_Framework_TestCase;
use Uma\Domain\Exceptions\DomainException;

/**
 * Unit tests for the Genre entity.
 *
 * @package Uma\Domain\Model;
 */
class GenreTest extends PHPUnit_Framework_TestCase
{
    const FIXTURE_DIR = __DIR__ . '/Fixtures/Genre/';

    /** @var Loader */
    private $alice;

    public function setUp()
    {
        parent::setUp();
        $this->alice = new Loader('en_US');
    }

    public function testConstruct()
    {
        $testClass = new Genre('what a name');

        $this->assertEquals('what a name', $testClass->getName());
        $this->assertEquals([], $testClass->getMovies());
        $this->assertEquals([], $testClass->getActors());
    }

    public function testNameTooSmall()
    {
        /** @var Genre[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'GenreStub.yml');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Genre name invalid');

        $testClass = $fixtures['Genre'];
        $testClass->setName('');
    }

    public function testNameTooLong()
    {
        /** @var Genre[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'GenreStub.yml');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Genre name invalid');

        $testClass = $fixtures['Genre'];
        $testClass->setName(str_repeat('a', 256));
    }

    public function testAddMovie()
    {
        /** @var Genre[]|Movie[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Movies.yml');

        $testClass = $fixtures['Genre'];
        $testClass->addMovie($fixtures['NewMovie']);

        $expectedMovies = [$fixtures['AddedMovie'], $fixtures['NewMovie']];
        $this->assertEquals($expectedMovies, $testClass->getMovies());
    }

    public function testAddMovieAlreadyExists()
    {
        /** @var Genre[]|Movie[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Movies.yml');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Movie already within genre');

        $testClass = $fixtures['Genre'];
        $testClass->addMovie($fixtures['AddedMovie']);
    }

    public function testAddActor()
    {
        /** @var Genre[]|Actor[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Actors.yml');

        $testClass = $fixtures['Genre'];
        $testClass->addActor($fixtures['NewActor']);

        $expectedActors = [$fixtures['AddedActor'], $fixtures['NewActor']];
        $this->assertEquals($expectedActors, $testClass->getActors());
    }

    public function testGetActorsFromMovies()
    {
        /** @var Genre[]|Actor[]|Movie[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'GetActorsFromMovies.yml');
        $fixtures['Movie']->addActor('another character', $fixtures['Actor1']);

        $testClass = $fixtures['Genre'];
        $testClass->addActor($fixtures['Actor2']);

        $expectedActors = [$fixtures['DuplicateActor'], $fixtures['Actor1'], $fixtures['Actor2']];
        $this->assertEquals($expectedActors, $testClass->getActors());
    }

    public function testAddActorAlreadyExists()
    {
        /** @var Genre[]|Actor[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Actors.yml');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Actor already within genre');

        $testClass = $fixtures['Genre'];
        $testClass->addActor($fixtures['AddedActor']);
    }

    public function testRemoveMovie()
    {
        /** @var Genre[]|Movie[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Movies.yml');

        $testClass = $fixtures['Genre'];
        $testClass->removeMovie($fixtures['AddedMovie']);

        $this->assertEquals([], $testClass->getMovies());
    }

    public function testRemoveMovieDoesNotExist()
    {
        /** @var Genre[]|Movie[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Movies.yml');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Movie not within genre');

        $testClass = $fixtures['Genre'];
        $testClass->removeMovie($fixtures['NewMovie']);
    }

    public function testRemoveActor()
    {
        /** @var Genre[]|Actor[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Actors.yml');

        $testClass = $fixtures['Genre'];
        $testClass->removeActor($fixtures['AddedActor']);

        $this->assertEquals([], $testClass->getActors());
    }

    public function testRemoveActorDoesNotExist()
    {
        /** @var Genre[]|Actor[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Actors.yml');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Actor not within genre');

        $testClass = $fixtures['Genre'];
        $testClass->removeActor($fixtures['NewActor']);
    }
}
