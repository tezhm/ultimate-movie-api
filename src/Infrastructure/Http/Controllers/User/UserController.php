<?php declare(strict_types=1);
namespace Uma\Infrastructure\Http\Controllers\User;

use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Uma\Domain\Exceptions\NoResourceException;
use Uma\Domain\Model\Movie;
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
     * @SWG\Post(
     *     path="/user",
     *     tags={"user"},
     *     operationId="createUser",
     *     summary="Creates a new user",
     *     description="",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Username of the user",
     *         in="formData",
     *         name="username",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="Password of the user",
     *         in="formData",
     *         name="password",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     security={{"uma_auth":{"write:users", "read:users"}}}
     * )
     *
     * @param Request $request
     * @return Response
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

        return response('', Response::HTTP_CREATED);
    }

    /**
     * @SWG\Put(
     *     path="/user/favourite",
     *     tags={"user"},
     *     operationId="addFavouriteToUser",
     *     summary="Adds a movie as a favourite for current user",
     *     description="",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Name of the movie",
     *         in="formData",
     *         name="movie",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     security={{"uma_auth":{"write:users", "read:users"}}}
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function addFavourite(Request $request)
    {
        $this->validate($request, ['movie' => 'required|string']);

        $this->entityManager->transactional(function() use($request)
        {
            $movie = $this->findMovie($request->post('movie'));
            /** @var User $user */
            $user = $request->user('api');
            $user->addFavourite($movie);
        });

        return response('', Response::HTTP_OK);
    }

    /**
     * @SWG\Delete(
     *     path="/user/favourite",
     *     tags={"user"},
     *     operationId="removeFavouriteFromUser",
     *     summary="Removes a movie as a favourite from current user",
     *     description="",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Name of the movie",
     *         in="formData",
     *         name="movie",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     security={{"uma_auth":{"write:users", "read:users"}}}
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function removeFavourite(Request $request)
    {
        $this->validate($request, ['movie' => 'required|string']);

        $this->entityManager->transactional(function() use($request)
        {
            $movie = $this->findMovie($request->post('movie'));
            /** @var User $user */
            $user = $request->user('api');
            $user->removeFavourite($movie);
        });

        return response('', Response::HTTP_OK);
    }

    /**
     * @SWG\Get(
     *     path="/user",
     *     tags={"user"},
     *     operationId="getUser",
     *     summary="Gets the current user information",
     *     description="",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     security={{"uma_auth":{"write:users", "read:users"}}}
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function show(Request $request)
    {
        $user = $request->user('api');
        return response(json_encode($user), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    /**
     * Attempts to find the movie by name.
     *
     * @param string $name
     * @return Movie
     */
    private function findMovie(string $name): Movie
    {
        $movie = $this->movieRepository->showByName($name);

        if ($movie === null)
        {
            throw new NoResourceException('Movie does not exist');
        }

        return $movie;
    }
}
