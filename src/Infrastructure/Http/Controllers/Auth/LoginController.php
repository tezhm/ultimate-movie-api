<?php declare(strict_types=1);
namespace Uma\Infrastructure\Http\Controllers\Auth;

use Doctrine\ORM\EntityManager;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Response;
use Uma\Domain\Model\User;
use Uma\Infrastructure\Http\Controllers\Controller;

/**
 * Handles login requests.
 *
 * @package Uma\Infrastructure\Http\Controllers\Auth
 */
class LoginController extends Controller
{
    /** @var Auth */
    private $auth;
    /** @var EntityManager */
    private $entityManager;

    /**
     * Create a new login controller instance.
     *
     * @param Auth $auth
     * @param EntityManager $entityManager
     */
    public function __construct(Auth $auth, EntityManager $entityManager)
    {
        $this->auth = $auth;
        $this->entityManager = $entityManager;
    }

    /**
     * Returns a new token if credentials are valid.
     *
     * @param Request $request
     * @return Response|\Laravel\Lumen\Http\ResponseFactory|string
     */
    public function login(Request $request)
    {
        $this->validate($request, ['username' => 'required', 'password' => 'required']);

        $credentials = [
            'username' => $request->post('username'),
            'password' => $request->post('password')
        ];

        /** @var StatefulGuard $guard */
        $guard = $this->auth->guard('web');

        if (!$guard->attempt($credentials))
        {
            return response(null, Response::HTTP_UNAUTHORIZED);
        }

        $token = null;
        $this->entityManager->transactional(function() use($guard, &$token)
        {
            /** @var User $user */
            $user = $guard->user();
            $user->generateApiToken();
            $token = $user->getApiToken();
        });

        $response = ['token' => $token];
        return response(json_encode($response), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}
