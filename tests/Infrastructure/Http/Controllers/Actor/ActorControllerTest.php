<?php declare(strict_types=1);
namespace Uma\Infrastructure\Http\Controllers\Actor;

use DateTime;
use Illuminate\Http\Response;
use Uma\DatabaseTransactions;
use Uma\Domain\Model\User;
use Uma\LumenTest;

/**
 * Integration tests for the actor controller.
 *
 * @package Uma\Infrastructure\Http\Controllers\Actor
 */
class ActorControllerTest extends LumenTest
{
    const FIXTURE_DIR = __DIR__ . '/Fixtures/';

    use DatabaseTransactions;

    public function testCreate()
    {
        $token = $this->generateToken();

        $command = ['api_token' => $token, 'name' => 'new actor', 'birth' => (new DateTime('now'))->format(DateTime::ATOM)];
        $this->json('POST', 'actor/create', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $query = ['api_token' => $token, 'name' => 'new actor'];
        $this->json('GET', 'actor/show', $query)
             ->seeStatusCode(Response::HTTP_OK)
             ->seeHeader('Content-Type', 'application/json')
             ->seeJsonEquals([
                 'name' => 'new actor',
                 'birth' => (new DateTime('now'))->format(DateTime::ATOM),
                 'age' => 0,
                 'bio' => null,
                 'image' => null,
             ]);
    }

    public function testRemove()
    {
        $token = $this->generateToken();

        $command = ['api_token' => $token, 'name' => 'new actor', 'birth' => (new DateTime('now'))->format(DateTime::ATOM)];
        $this->json('POST', 'actor/create', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $command = ['api_token' => $token, 'name' => 'new actor'];
        $this->json('POST', 'actor/remove', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $query = ['api_token' => $token, 'name' => 'new actor'];
        $this->json('GET', 'actor/show', $query)
             ->seeStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testChange()
    {
        $token = $this->generateToken();

        $command = ['api_token' => $token, 'name' => 'new actor', 'birth' => (new DateTime('now'))->format(DateTime::ATOM)];
        $this->json('POST', 'actor/create', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $command = [
            'api_token' => $token,
            'name' => 'new actor',
            'birth' => (new DateTime('last year'))->format(DateTime::ATOM),
            'bio' => 'this is a bio',
            'image' => 'im not really an image lol'
        ];
        $this->json('POST', 'actor/change', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $query = ['api_token' => $token, 'name' => 'new actor'];
        $this->json('GET', 'actor/show', $query)
             ->seeStatusCode(Response::HTTP_OK)
             ->seeHeader('Content-Type', 'application/json')
             ->seeJsonEquals([
                 'name' => 'new actor',
                 'birth' => (new DateTime('last year'))->format(DateTime::ATOM),
                 'age' => 1,
                 'bio' => 'this is a bio',
                 'image' => 'im not really an image lol'
             ]);
    }

    public function testIndex()
    {
        $token = $this->generateToken();

        $command = ['api_token' => $token, 'name' => 'actor1', 'birth' => '2017-09-17T02:24:16+00:00'];
        $this->json('POST', 'actor/create', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $command = ['api_token' => $token, 'name' => 'actor2', 'birth' => '2017-09-17T02:23:16+00:00'];
        $this->json('POST', 'actor/create', $command)
             ->seeStatusCode(Response::HTTP_OK);

        $query = ['api_token' => $token];
        $this->json('GET', 'actor/index', $query)
             ->seeStatusCode(Response::HTTP_OK)
             ->seeHeader('Content-Type', 'application/json')
             ->seeJsonEquals([
                 [
                     'name' => 'actor1',
                     'birth' => '2017-09-17T02:24:16+00:00',
                     'age' => 0,
                     'bio' => null,
                     'image' => null,
                 ],
                 [
                     'name' => 'actor2',
                     'birth' => '2017-09-17T02:23:16+00:00',
                     'age' => 0,
                     'bio' => null,
                     'image' => null,
                 ],
             ]);
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
