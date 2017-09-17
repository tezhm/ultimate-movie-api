<?php declare(strict_types=1);
namespace Uma\Infrastructure\Http\Controllers\Genre;

use Illuminate\Http\Response;
use Uma\DatabaseTransactions;
use Uma\Domain\Model\Actor;
use Uma\Domain\Model\User;
use Uma\LumenTest;

/**
 * Integration tests for the genre controller.
 *
 * @package Uma\Infrastructure\Http\Controllers\Genre
 */
class GenreControllerTest extends LumenTest
{
    const FIXTURE_DIR = __DIR__ . '/Fixtures/';

    use DatabaseTransactions;

    public function testCreate()
    {
        $token = $this->generateToken();

        $command = ['api_token' => $token, 'name' => 'new genre'];
        $this->json('POST', 'genre/create', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $query = ['api_token' => $token, 'name' => 'new genre'];
        $this->json('GET', 'genre/show', $query)
             ->seeStatusCode(Response::HTTP_OK)
             ->seeHeader('Content-Type', 'application/json')
             ->seeJsonEquals(['name' => 'new genre', 'movies' => [], 'actors' => []]);
    }

    public function testRemove()
    {
        $token = $this->generateToken();

        $command = ['api_token' => $token, 'name' => 'new genre'];
        $this->json('POST', 'genre/create', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $command = ['api_token' => $token, 'name' => 'new genre'];
        $this->json('POST', 'genre/remove', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $query = ['api_token' => $token, 'name' => 'new genre'];
        $this->json('GET', 'genre/show', $query)
             ->seeStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testAddActor()
    {
        $token = $this->generateToken();
        $actors = $this->seedActors();

        $command = ['api_token' => $token, 'name' => 'new genre'];
        $this->json('POST', 'genre/create', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $command = ['api_token' => $token, 'name' => 'new genre', 'actor' => $actors[0]];
        $this->json('POST', 'genre/add/actor', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $command = ['api_token' => $token, 'name' => 'new genre', 'actor' => $actors[1]];
        $this->json('POST', 'genre/add/actor', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $query = ['api_token' => $token, 'name' => 'new genre'];
        $this->json('GET', 'genre/show', $query)
             ->seeStatusCode(Response::HTTP_OK)
             ->seeHeader('Content-Type', 'application/json')
             ->seeJsonEquals(['name' => 'new genre', 'movies' => [], 'actors' => $actors]);
    }

    public function testAddMovie()
    {
        $token = $this->generateToken();
        $movies = $this->seedMovies();

        $command = ['api_token' => $token, 'name' => 'new genre'];
        $this->json('POST', 'genre/create', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $command = ['api_token' => $token, 'name' => 'new genre', 'movie' => $movies[0]];
        $this->json('POST', 'genre/add/movie', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $command = ['api_token' => $token, 'name' => 'new genre', 'movie' => $movies[1]];
        $this->json('POST', 'genre/add/movie', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $query = ['api_token' => $token, 'name' => 'new genre'];
        $this->json('GET', 'genre/show', $query)
             ->seeStatusCode(Response::HTTP_OK)
             ->seeHeader('Content-Type', 'application/json')
             ->seeJsonEquals(['name' => 'new genre', 'movies' => $movies, 'actors' => []]);
    }

    public function testRemoveActor()
    {
        $token = $this->generateToken();
        $actors = $this->seedActors();

        $command = ['api_token' => $token, 'name' => 'new genre'];
        $this->json('POST', 'genre/create', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $command = ['api_token' => $token, 'name' => 'new genre', 'actor' => $actors[0]];
        $this->json('POST', 'genre/add/actor', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $command = ['api_token' => $token, 'name' => 'new genre', 'actor' => $actors[0]];
        $this->json('POST', 'genre/remove/actor', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $query = ['api_token' => $token, 'name' => 'new genre'];
        $this->json('GET', 'genre/show', $query)
             ->seeStatusCode(Response::HTTP_OK)
             ->seeHeader('Content-Type', 'application/json')
             ->seeJsonEquals(['name' => 'new genre', 'movies' => [], 'actors' => []]);
    }

    public function testRemoveMovie()
    {
        $token = $this->generateToken();
        $movies = $this->seedMovies();

        $command = ['api_token' => $token, 'name' => 'new genre'];
        $this->json('POST', 'genre/create', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $command = ['api_token' => $token, 'name' => 'new genre', 'movie' => $movies[0]];
        $this->json('POST', 'genre/add/movie', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $command = ['api_token' => $token, 'name' => 'new genre', 'movie' => $movies[0]];
        $this->json('POST', 'genre/remove/movie', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $query = ['api_token' => $token, 'name' => 'new genre'];
        $this->json('GET', 'genre/show', $query)
             ->seeStatusCode(Response::HTTP_OK)
             ->seeHeader('Content-Type', 'application/json')
             ->seeJsonEquals(['name' => 'new genre', 'movies' => [], 'actors' => []]);
    }

    public function testIndex()
    {
        $token = $this->generateToken();

        $command = ['api_token' => $token, 'name' => 'genre1'];
        $this->json('POST', 'genre/create', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $command = ['api_token' => $token, 'name' => 'genre2'];
        $this->json('POST', 'genre/create', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $query = ['api_token' => $token];
        $this->json('GET', 'genre/index', $query)
             ->seeStatusCode(Response::HTTP_OK)
             ->seeHeader('Content-Type', 'application/json')
             ->seeJsonEquals([
                 ['name' => 'genre1', 'movies' => [], 'actors' => []],
                 ['name' => 'genre2', 'movies' => [], 'actors' => []],
             ]);
    }

    private function seedActors()
    {
        /** @var Actor[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Actors.yml');

        $this->entityManager->persist($fixtures['Actor1']);
        $this->entityManager->persist($fixtures['Actor2']);
        $this->entityManager->flush();

        return [$fixtures['Actor1']->getName(), $fixtures['Actor2']->getName()];
    }

    private function seedMovies()
    {
        /** @var Actor[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'Movies.yml');

        $this->entityManager->persist($fixtures['Movie1']);
        $this->entityManager->persist($fixtures['Movie2']);
        $this->entityManager->flush();

        return [$fixtures['Movie1']->getName(), $fixtures['Movie2']->getName()];
    }

    private function generateToken(): string
    {
        /** @var User[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'UserStub.yml');
        $fixtures['User']->generateApiToken();

        $this->entityManager->persist($fixtures['User']);
        $this->entityManager->flush();

        return $fixtures['User']->getApiToken();
    }
}
