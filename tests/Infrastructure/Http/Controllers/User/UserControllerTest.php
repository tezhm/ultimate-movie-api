<?php declare(strict_types=1);
namespace Uma\Infrastructure\Http\Controllers\Movie;

use Illuminate\Http\Response;
use Uma\DatabaseTransactions;
use Uma\Domain\Model\Movie;
use Uma\LumenTest;

/**
 * Integration tests for the movie controller.
 *
 * @package Uma\Infrastructure\Http\Controllers\Movie
 */
class UserControllerTest extends LumenTest
{
    use DatabaseTransactions;

    public function testCreate()
    {
        $command = ['username' => 'newuser', 'password' => 'password123'];
        $this->json('POST', 'user', $command)
             ->seeStatusCode(Response::HTTP_CREATED);

        $command = ['username' => 'newuser', 'password' => 'password123'];
        $response = $this->json('POST', 'user/login', $command)
                         ->seeStatusCode(Response::HTTP_OK)
                         ->response;

        $token = json_decode($response->getContent(), true)['api_token'];

        $query = ['api_token' => $token];
        $this->json('GET', 'user', $query)
             ->seeStatusCode(Response::HTTP_OK)
             ->seeHeader('Content-Type', 'application/json')
             ->seeJson(['username' => 'newuser', 'favourites' => []]);
    }

    public function testAddFavourite()
    {
        $movies = $this->seedMovies();

        $command = ['username' => 'newuser', 'password' => 'password123'];
        $this->json('POST', 'user', $command)
             ->seeStatusCode(Response::HTTP_CREATED);

        $command = ['username' => 'newuser', 'password' => 'password123'];
        $response = $this->json('POST', 'user/login', $command)
                         ->seeStatusCode(Response::HTTP_OK)
                         ->response;

        $token = json_decode($response->getContent(), true)['api_token'];

        $command = ['api_token' => $token, 'movie' => $movies[0]];
        $this->json('PUT', 'user/favourite', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $command = ['api_token' => $token, 'movie' => $movies[1]];
        $this->json('PUT', 'user/favourite', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $query = ['api_token' => $token];
        $this->json('GET', 'user', $query)
             ->seeStatusCode(Response::HTTP_OK)
             ->seeHeader('Content-Type', 'application/json')
             ->seeJson(['username' => 'newuser', 'favourites' => $movies]);
    }

    public function testRemoveFavourite()
    {
        $movies = $this->seedMovies();

        $command = ['username' => 'newuser', 'password' => 'password123'];
        $this->json('POST', 'user', $command)
            ->seeStatusCode(Response::HTTP_CREATED);

        $command = ['username' => 'newuser', 'password' => 'password123'];
        $response = $this->json('POST', 'user/login', $command)
                         ->seeStatusCode(Response::HTTP_OK)
                         ->response;

        $token = json_decode($response->getContent(), true)['api_token'];

        $command = ['api_token' => $token, 'movie' => $movies[0]];
        $this->json('PUT', 'user/favourite', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $command = ['api_token' => $token, 'movie' => $movies[0]];
        $this->json('DELETE', 'user/favourite', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $query = ['api_token' => $token];
        $this->json('GET', 'user', $query)
             ->seeStatusCode(Response::HTTP_OK)
             ->seeHeader('Content-Type', 'application/json')
             ->seeJson(['username' => 'newuser', 'favourites' => []]);
    }

    private function seedMovies()
    {
        /** @var Movie[] $fixtures */
        $fixtures = $this->alice->load(__DIR__ . '/Fixtures/Movies.yml');

        $this->entityManager->persist($fixtures['Movie1']);
        $this->entityManager->persist($fixtures['Movie2']);
        $this->entityManager->flush();

        return [$fixtures['Movie1']->getName(), $fixtures['Movie2']->getName()];
    }
}
