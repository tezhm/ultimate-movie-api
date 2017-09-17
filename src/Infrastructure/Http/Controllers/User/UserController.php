<?php declare(strict_types=1);
namespace Uma\Infrastructure\Http\Controllers\User;

use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Uma\Domain\Exceptions\DomainException;
use Uma\Domain\Model\MovieRepository;
use Uma\Domain\Model\User;
use Uma\Domain\Model\UserRepository;
use Uma\Infrastructure\Http\Controllers\Controller;

/**
 * Handles commands and queries for Users.
 *
 * @package Uma\Infrastructure\Http\Controllers\User
 */
class UserController extends Controller
{
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var UserRepository */
    private $userRepository;
    /** @var MovieRepository */
    private $movieRepository;

    /**
     * Create a new user controller instance.
     *
     * @param EntityManagerInterface $entityManager
     * @param UserRepository         $userRepository
     * @param MovieRepository        $movieRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        MovieRepository $movieRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->movieRepository = $movieRepository;
    }

    /**
     * TODO: swagger documentation
     *
     * @param Request $request
     */
    public function create(Request $request)
    {
        $this->validate($request, ['username' => 'required|string', 'password' => 'required|string']);

        $this->entityManager->transactional(function() use($request)
        {
            $user = new User(
                $request->post('username'),
                $request->post('password')
            );
            $this->userRepository->add($user);
        });
    }

    /**
     * TODO: swagger documentation
     *
     * @param Request $request
     */
    public function addFavourite(Request $request)
    {
        $this->validate($request, ['movie' => 'required|string']);

        $this->entityManager->transactional(function() use($request)
        {
            $movie = $this->movieRepository->showByName($request->post('movie'));

            if ($movie === null)
            {
                throw new DomainException('Movie does not exist');
            }

            /** @var User $user */
            $user = $request->user('api');
            $user->addFavourite($movie);
        });
    }

    /**
     * TODO: swagger documentation
     *
     * @param Request $request
     */
    public function removeFavourite(Request $request)
    {
        $this->validate($request, ['movie' => 'required|string']);

        $this->entityManager->transactional(function() use($request)
        {
            $movie = $this->movieRepository->showByName($request->post('movie'));

            if ($movie === null)
            {
                throw new DomainException('Movie does not exist');
            }

            /** @var User $user */
            $user = $request->user('api');
            $user->removeFavourite($movie);
        });
    }

    /**
     * TODO: swagger documentation
     *
     * @param Request $request
     * @return Response
     */
    public function show(Request $request)
    {
        $user = $request->user('api');
        return response(json_encode($user), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}
