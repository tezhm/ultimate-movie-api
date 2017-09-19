<?php declare(strict_types=1);
namespace Uma\Infrastructure\Http\Controllers\Movie;

use Illuminate\Http\Response;
use Uma\DatabaseTransactions;
use Uma\Domain\Model\Actor;
use Uma\Domain\Model\User;
use Uma\LumenTest;

/**
 * Integration tests for the movie controller.
 *
 * @package Uma\Infrastructure\Http\Controllers\Movie
 */
class MovieControllerTest extends LumenTest
{
    const FIXTURE_DIR = __DIR__ . '/Fixtures/';

    use DatabaseTransactions;

    public function testCreate()
    {
        $token = $this->generateToken();

        $command = ['api_token' => $token, 'name' => 'new movie'];
        $this->json('POST', 'movie', $command)
             ->seeStatusCode(Response::HTTP_CREATED);

        $query = ['api_token' => $token, 'name' => 'new movie'];
        $this->json('GET', 'movie', $query)
             ->seeStatusCode(Response::HTTP_OK)
             ->seeHeader('Content-Type', 'application/json')
             ->seeJsonEquals([
                 'name' => 'new movie',
                 'genre' => null,
                 'actors' => [],
                 'rating' => 0,
                 'description' => null,
                 'image' => null
             ]);
    }

    public function testRemove()
    {
        $token = $this->generateToken();

        $command = ['api_token' => $token, 'name' => 'new movie'];
        $this->json('POST', 'movie', $command)
             ->seeStatusCode(Response::HTTP_CREATED);

        $command = ['api_token' => $token, 'name' => 'new movie'];
        $this->json('DELETE', 'movie', $command)
             ->seeStatusCode(Response::HTTP_NO_CONTENT);

        $query = ['api_token' => $token, 'name' => 'new movie'];
        $this->json('GET', 'movie', $query)
             ->seeStatusCode(Response::HTTP_NOT_FOUND);
    }

    public function testChange()
    {
        $token = $this->generateToken();
        $genre = $this->seedGenre();

        $command = ['api_token' => $token, 'name' => 'new movie'];
        $this->json('POST', 'movie', $command)
             ->seeStatusCode(Response::HTTP_CREATED);

        $command = [
            'api_token' => $token,
            'name' => 'new movie',
            'genre' => $genre,
            'rating' => 3,
            'description' => 'an auto-biography of documentary documenters',
            'image' => 'this is an image 4relz',
        ];
        $this->json('PUT', 'movie', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $query = ['api_token' => $token, 'name' => 'new movie'];
        $this->json('GET', 'movie', $query)
             ->seeStatusCode(Response::HTTP_OK)
             ->seeHeader('Content-Type', 'application/json')
             ->seeJsonEquals([
                 'name' => 'new movie',
                 'genre' => $genre,
                 'actors' => [],
                 'rating' => 3,
                 'description' => 'an auto-biography of documentary documenters',
                 'image' => 'this is an image 4relz',
             ]);
    }

    public function testAddActor()
    {
        $token = $this->generateToken();
        $actors = $this->seedActors();

        $command = ['api_token' => $token, 'name' => 'new movie'];
        $this->json('POST', 'movie', $command)
             ->seeStatusCode(Response::HTTP_CREATED);

        $command = ['api_token' => $token, 'name' => 'new movie', 'actor' => $actors[0], 'character' => 'megatron'];
        $this->json('PUT', 'movie/actor', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $command = ['api_token' => $token, 'name' => 'new movie', 'actor' => $actors[1], 'character' => 'optimus prime'];
        $this->json('PUT', 'movie/actor', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $query = ['api_token' => $token, 'name' => 'new movie'];
        $this->json('GET', 'movie', $query)
             ->seeStatusCode(Response::HTTP_OK)
             ->seeHeader('Content-Type', 'application/json')
             ->seeJsonEquals([
                 'name' => 'new movie',
                 'genre' => null,
                 'actors' => ['megatron' => $actors[0], 'optimus prime' => $actors[1]],
                 'rating' => 0,
                 'description' => null,
                 'image' => null,
             ]);
    }

    public function testRemoveActor()
    {
        $token = $this->generateToken();
        $actors = $this->seedActors();

        $command = ['api_token' => $token, 'name' => 'new movie'];
        $this->json('POST', 'movie', $command)
             ->seeStatusCode(Response::HTTP_CREATED);

        $command = ['api_token' => $token, 'name' => 'new movie', 'actor' => $actors[0], 'character' => 'megatron'];
        $this->json('PUT', 'movie/actor', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $command = ['api_token' => $token, 'name' => 'new movie', 'actor' => $actors[0]];
        $this->json('DELETE', 'movie/actor', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $query = ['api_token' => $token, 'name' => 'new movie'];
        $this->json('GET', 'movie', $query)
             ->seeStatusCode(Response::HTTP_OK)
             ->seeHeader('Content-Type', 'application/json')
             ->seeJsonEquals([
                 'name' => 'new movie',
                 'genre' => null,
                 'actors' => [],
                 'rating' => 0,
                 'description' => null,
                 'image' => null,
             ]);
    }

    public function testIndex()
    {
        $token = $this->generateToken();

        $command = ['api_token' => $token, 'name' => 'movie1'];
        $this->json('POST', 'movie', $command)
             ->seeStatusCode(Response::HTTP_CREATED);

        $command = ['api_token' => $token, 'name' => 'movie2'];
        $this->json('POST', 'movie', $command)
             ->seeStatusCode(Response::HTTP_CREATED);

        $query = ['api_token' => $token];
        $this->json('GET', 'movies', $query)
             ->seeStatusCode(Response::HTTP_OK)
             ->seeHeader('Content-Type', 'application/json')
             ->seeJsonEquals([
                 [
                     'name' => 'movie1',
                     'genre' => null,
                     'actors' => [],
                     'rating' => 0,
                     'description' => null,
                     'image' => null
                 ],
                 [
                     'name' => 'movie2',
                     'genre' => null,
                     'actors' => [],
                     'rating' => 0,
                     'description' => null,
                     'image' => null
                 ],
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

    private function seedGenre()
    {
        /** @var Actor[] $fixtures */
        $fixtures = $this->alice->load(self::FIXTURE_DIR . 'GenreStub.yml');

        $this->entityManager->persist($fixtures['Genre']);
        $this->entityManager->flush();

        return $fixtures['Genre']->getName();
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
