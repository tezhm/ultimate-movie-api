<?php declare(strict_types=1);
namespace Uma\Infrastructure\Http\Controllers\Movie;

use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Uma\Domain\Exceptions\NoResourceException;
use Uma\Domain\Model\Actor;
use Uma\Domain\Model\ActorRepository;
use Uma\Domain\Model\Genre;
use Uma\Domain\Model\GenreRepository;
use Uma\Domain\Model\Movie;
use Uma\Domain\Model\MovieRepository;
use Uma\Domain\Model\User;
use Uma\Infrastructure\Http\Controllers\Controller;

/**
 * Handles commands and queries for Movies.
 *
 * @package Uma\Infrastructure\Http\Controllers\Movie
 */
class MovieController extends Controller
{
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var MovieRepository */
    private $genreRepository;
    /** @var ActorRepository */
    private $actorRepository;
    /** @var MovieRepository */
    private $movieRepository;

    /**
     * Create a new movie controller instance.
     *
     * @param EntityManagerInterface $entityManager
     * @param MovieRepository        $movieRepository
     * @param ActorRepository        $actorRepository
     * @param GenreRepository        $genreRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        MovieRepository $movieRepository,
        ActorRepository $actorRepository,
        GenreRepository $genreRepository)
    {
        $this->entityManager = $entityManager;
        $this->movieRepository = $movieRepository;
        $this->actorRepository = $actorRepository;
        $this->genreRepository = $genreRepository;
    }

    /**
     * @SWG\Post(
     *     path="/movie",
     *     tags={"movie"},
     *     operationId="createMovie",
     *     summary="Creates a new movie",
     *     description="",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Name of the movie",
     *         in="formData",
     *         name="name",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     security={{"uma_auth":{"write:movies", "read:movies"}}}
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        $this->validate($request, ['name' => 'required|string']);

        $this->entityManager->transactional(function() use($request)
        {
            $movie = new Movie($request->post('name'));
            $this->movieRepository->add($movie);
        });

        return response('', Response::HTTP_CREATED);
    }

    /**
     * @SWG\Delete(
     *     path="/movie",
     *     tags={"movie"},
     *     operationId="removeMovie",
     *     summary="Removes a movie",
     *     description="",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Name of the movie",
     *         in="formData",
     *         name="name",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     security={{"uma_auth":{"write:movies", "read:movies"}}}
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function remove(Request $request)
    {
        $this->validate($request, ['name' => 'required|string']);

        $this->entityManager->transactional(function() use($request)
        {
            $movie = $this->findMovie($request->post('name'));
            $this->movieRepository->remove($movie);
        });

        return response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @SWG\Put(
     *     path="/movie",
     *     tags={"movie"},
     *     operationId="changeMovie",
     *     summary="Change the values of a movie",
     *     description="",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Name of the movie",
     *         in="formData",
     *         name="name",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="Genre to set the movie as",
     *         in="formData",
     *         name="genre",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="Rating as given by the current user",
     *         in="formData",
     *         name="rating",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         description="Description of the movie",
     *         in="formData",
     *         name="description",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="Image of the movie",
     *         in="formData",
     *         name="image",
     *         required=false,
     *         type="string",
     *         format="byte"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     security={{"uma_auth":{"write:movies", "read:movies"}}}
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function change(Request $request)
    {
        $rules = [
            'name'        => 'required|string',
            'genre'       => 'string',
            'rating'      => 'int',
            'description' => 'string',
            'image'       => 'string',
        ];
        $this->validate($request, $rules);

        $this->entityManager->transactional(function() use($request)
        {
            $movie = $this->findMovie($request->post('name'));

            if ($request->has('genre'))
            {
                $genre = $this->findGenre($request->post('genre'));
                $movie->setGenre($genre);
            }

            if ($request->has('rating'))
            {
                /** @var User $user */
                $user = $request->user('api');
                $movie->addRating($user->getAuthIdentifier(), (int)$request->post('rating'));
            }

            if ($request->has('description'))
            {
                $movie->setDescription($request->post('description'));
            }

            if ($request->has('image'))
            {
                $movie->setImage($request->post('image'));
            }
        });

        return response('', Response::HTTP_OK);
    }

    /**
     * @SWG\Put(
     *     path="/movie/actor",
     *     tags={"movie"},
     *     operationId="addActorToMovie",
     *     summary="Adds an actor to the movie",
     *     description="",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Name of the movie",
     *         in="formData",
     *         name="name",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="Name of actor to add to movie",
     *         in="formData",
     *         name="actor",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="Name of character the actor played",
     *         in="formData",
     *         name="character",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     security={{"uma_auth":{"write:movies", "read:movies"}}}
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function addActor(Request $request)
    {
        $rules = ['name' => 'required|string', 'actor' => 'required|string', 'character' => 'required|string'];
        $this->validate($request, $rules);

        $this->entityManager->transactional(function() use($request)
        {
            $movie = $this->findMovie($request->post('name'));
            $actor = $this->findActor($request->post('actor'));
            $movie->addActor($request->post('character'), $actor);
        });

        return response('', Response::HTTP_OK);
    }

    /**
     * @SWG\Delete(
     *     path="/movie/actor",
     *     tags={"movie"},
     *     operationId="removeActorFromMovie",
     *     summary="Removes an actor from a movie",
     *     description="",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Name of the movie",
     *         in="formData",
     *         name="name",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="Name of actor to add to movie",
     *         in="formData",
     *         name="actor",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     security={{"uma_auth":{"write:movies", "read:movies"}}}
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function removeActor(Request $request)
    {
        $rules = ['name' => 'required|string', 'actor' => 'required|string'];
        $this->validate($request, $rules);

        $this->entityManager->transactional(function() use($request)
        {
            $movie = $this->findMovie($request->post('name'));
            $actor = $this->findActor($request->post('actor'));
            $movie->removeActor($actor);
        });

        return response('', Response::HTTP_OK);
    }

    /**
     * @SWG\Get(
     *     path="/movie",
     *     tags={"movie"},
     *     operationId="getMovie",
     *     summary="Retrieve a movie by name",
     *     description="",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Name of the movie",
     *         in="formData",
     *         name="name",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     security={{"uma_auth":{"write:movies", "read:movies"}}}
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function show(Request $request)
    {
        $this->validate($request, ['name' => 'required|string']);
        $movie = $this->findMovie($request->post('name'));
        return response(json_encode($movie), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    /**
     * @SWG\Get(
     *     path="/movies",
     *     tags={"movie"},
     *     operationId="getMovies",
     *     summary="Retrieve all movies",
     *     description="",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     security={{"uma_auth":{"write:movies", "read:movies"}}}
     * )
     *
     * @return Response
     */
    public function index()
    {
        $movies = $this->movieRepository->index();
        return response(json_encode($movies), Response::HTTP_OK, ['Content-Type' => 'application/json']);
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

    /**
     * Attempts to find the genre by name.
     *
     * @param string $name
     * @return Genre
     */
    private function findGenre(string $name): Genre
    {
        $genre = $this->genreRepository->showByName($name);

        if ($genre === null)
        {
            throw new NoResourceException('Genre does not exist');
        }

        return $genre;
    }

    /**
     * Attempts to find the actor by name.
     *
     * @param string $name
     * @return Actor
     */
    private function findActor(string $name): Actor
    {
        $actor = $this->actorRepository->showByName($name);

        if ($actor === null)
        {
            throw new NoResourceException('Actor does not exist');
        }

        return $actor;
    }
}
