<?php declare(strict_types=1);
namespace Uma\Domain\Model;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Hashing\Hasher;
use Mockery;
use Mockery\MockInterface;
use Nelmio\Alice\Fixtures\Loader;
use PHPUnit_Framework_TestCase;
use Uma\Domain\Exceptions\DomainException;

/**
 * Unit tests for the User entity.
 *
 * @package Uma\Domain\Model;
 */
class UserTest extends PHPUnit_Framework_TestCase
{
    /** @var Loader */
    private $alice;
    /** @var Container|MockInterface */
    private $mockContainer;
    /** @var Hasher|MockInterface */
    private $mockHasher;

    public function setUp()
    {
        parent::setUp();
        $this->alice = new Loader('en_US');
        $this->mockContainer = Mockery::mock(Container::class);
        $this->mockHasher = Mockery::mock(Hasher::class);
        DomainRegistry::setContainer($this->mockContainer);
        $this->mockContainer
             ->shouldReceive('make')
             ->with(Hasher::class)
             ->andReturn($this->mockHasher);
    }

    public function testConstruct()
    {
        $this->expectsHash('password123', 'hashedpassword');

        $testClass = new User('fred1E', 'password123');

        $this->assertEquals('fred1E', $testClass->getAuthIdentifier());
        $this->assertEquals('username', $testClass->getAuthIdentifierName());
        $this->assertEquals('hashedpassword', $testClass->getAuthPassword());
        $this->assertNull($testClass->getApiToken());
        $this->assertNull($testClass->getRememberToken());
        $this->assertNull($testClass->getRememberTokenName());
    }

    public function testUsernameTooSmall()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('User username invalid');

        new User('fre', 'password123');
    }

    public function testUsernameTooLong()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('User username invalid');

        new User(str_repeat('z', 17), 'password123');
    }

    public function testUsernameNotAscii()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('User username invalid');

        new User("ÿÿÿÿÿÿÿÿÿÿÿÿÿÿ", 'password123');
    }

    public function testUsernameNotAlphanumeric()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('User username invalid');

        new User("123azxc*(", 'password123');
    }

    public function testPasswordTooSmall()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('User password invalid');

        new User("potatoooo", 'passwor');
    }

    public function testPasswordTooLong()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('User password invalid');

        new User("potatoooo", str_repeat('v', 25));
    }

    public function testGenerateApiToken()
    {
        $this->expectsHash('password123', 'hashedpassword');

        $testClass = new User("potatoooo", 'password123');
        $testClass->generateApiToken();

        $this->assertNotNull($testClass->getApiToken());
    }

    public function testAddFavourite()
    {
        /** @var Movie[] $fixtures */
        $fixtures = $this->alice->load(__DIR__ . '/Fixtures/User/Movies.yml');
        $this->expectsHash('password123', 'hashedpassword');

        $testClass = new User("potatoooo", 'password123');
        $testClass->addFavourite($fixtures['Movie1']);
        $testClass->addFavourite($fixtures['Movie2']);

        $expected = [$fixtures['Movie1'], $fixtures['Movie2']];
        $this->assertEquals($expected, $testClass->getFavourites());
    }

    public function testAddFavouriteAlreadyExists()
    {
        /** @var Movie[] $fixtures */
        $fixtures = $this->alice->load(__DIR__ . '/Fixtures/User/Movies.yml');
        $this->expectsHash('password123', 'hashedpassword');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Movie already favourited');

        $testClass = new User("potatoooo", 'password123');
        $testClass->addFavourite($fixtures['Movie1']);
        $testClass->addFavourite($fixtures['Movie1']);
    }

    /**
     * Provides the make() method of Hasher.
     *
     * @param string $password
     * @param string $result
     */
    private function expectsHash(string $password, string $result)
    {
        $this->mockHasher
             ->shouldReceive('make')
             ->with($password)
             ->andReturn($result);
    }
}
