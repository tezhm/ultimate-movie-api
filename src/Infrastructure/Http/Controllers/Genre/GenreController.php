<?php declare(strict_types=1);
namespace Uma\Infrastructure\Http\Controllers\Genre;

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
use Uma\Infrastructure\Http\Controllers\Controller;

/**
 * Handles commands and queries for Genres.
 *
 * @package Uma\Infrastructure\Http\Controllers\Genre
 */
class GenreController extends Controller
{
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var GenreRepository */
    private $genreRepository;
    /** @var ActorRepository */
    private $actorRepository;
    /** @var MovieRepository */
    private $movieRepository;

    /**
     * Create a new genre controller instance.
     *
     * @param EntityManagerInterface $entityManager
     * @param GenreRepository $genreRepository
     * @param ActorRepository $actorRepository
     * @param MovieRepository $movieRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        GenreRepository $genreRepository,
        ActorRepository $actorRepository,
        MovieRepository $movieRepository)
    {
        $this->entityManager = $entityManager;
        $this->genreRepository = $genreRepository;
        $this->actorRepository = $actorRepository;
        $this->movieRepository = $movieRepository;
    }

    /**
     * @SWG\Post(
     *     path="/genre",
     *     tags={"genre"},
     *     operationId="createGenre",
     *     summary="Creates a new genre",
     *     description="",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Name of the genre",
     *         in="formData",
     *         name="name",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="successful operation"
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="resource not found"
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="failed validation"
     *     ),
     *     security={{"uma_auth":{"write:genres", "read:genres"}}}
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
            $genre = new Genre($request->post('name'));
            $this->genreRepository->add($genre);
        });

        return response('', Response::HTTP_CREATED);
    }

    /**
     * @SWG\Delete(
     *     path="/genre",
     *     tags={"genre"},
     *     operationId="removeGenre",
     *     summary="Removes a genre",
     *     description="",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Name of the genre",
     *         in="formData",
     *         name="name",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=204,
     *         description="successful operation"
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="resource not found"
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="failed validation"
     *     ),
     *     security={{"uma_auth":{"write:genres", "read:genres"}}}
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
            $genre = $this->findGenre($request->post('name'));
            $this->genreRepository->remove($genre);
        });

        return response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @SWG\Put(
     *     path="/genre/actor",
     *     tags={"genre"},
     *     operationId="addActorToGenre",
     *     summary="Adds an actor to the genre",
     *     description="",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Name of the genre",
     *         in="formData",
     *         name="name",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="Name of the actor",
     *         in="formData",
     *         name="actor",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="resource not found"
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="failed validation"
     *     ),
     *     security={{"uma_auth":{"write:genres", "read:genres"}}}
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function addActor(Request $request)
    {
        $this->validate($request, ['name' => 'required|string', 'actor' => 'required|string']);

        $this->entityManager->transactional(function() use($request)
        {
            $genre = $this->findGenre($request->post('name'));
            $actor = $this->findActor($request->post('actor'));
            $genre->addActor($actor);
        });

        return response('', Response::HTTP_OK);
    }

    /**
     * @SWG\Put(
     *     path="/genre/movie",
     *     tags={"genre"},
     *     operationId="addMovieToGenre",
     *     summary="Adds a movie to the genre",
     *     description="",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Name of the genre",
     *         in="formData",
     *         name="name",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="Name of the movie",
     *         in="formData",
     *         name="movie",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="resource not found"
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="failed validation"
     *     ),
     *     security={{"uma_auth":{"write:genres", "read:genres"}}}
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function addMovie(Request $request)
    {
        $this->validate($request, ['name' => 'required|string', 'movie' => 'required|string']);

        $this->entityManager->transactional(function() use($request)
        {
            $genre = $this->findGenre($request->post('name'));
            $movie = $this->findMovie($request->post('movie'));
            $genre->addMovie($movie);
        });

        return response('', Response::HTTP_OK);
    }

    /**
     * @SWG\Delete(
     *     path="/genre/actor",
     *     tags={"genre"},
     *     operationId="removeActorFromGenre",
     *     summary="Removes an actor from a genre",
     *     description="",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Name of the genre",
     *         in="formData",
     *         name="name",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="Name of the actor",
     *         in="formData",
     *         name="actor",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="resource not found"
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="failed validation"
     *     ),
     *     security={{"uma_auth":{"write:genres", "read:genres"}}}
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function removeActor(Request $request)
    {
        $this->validate($request, ['name' => 'required|string', 'actor' => 'required|string']);

        $this->entityManager->transactional(function() use($request)
        {
            $genre = $this->findGenre($request->post('name'));
            $actor = $this->findActor($request->post('actor'));
            $genre->removeActor($actor);
        });

        return response('', Response::HTTP_OK);
    }

    /**
     * @SWG\Delete(
     *     path="/genre/movie",
     *     tags={"genre"},
     *     operationId="removeMovieFromGenre",
     *     summary="Removes a movie from a genre",
     *     description="",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Name of the genre",
     *         in="formData",
     *         name="name",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="Name of the movie",
     *         in="formData",
     *         name="movie",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="resource not found"
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="failed validation"
     *     ),
     *     security={{"uma_auth":{"write:genres", "read:genres"}}}
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function removeMovie(Request $request)
    {
        $this->validate($request, ['name' => 'required|string', 'movie' => 'required|string']);

        $this->entityManager->transactional(function() use($request)
        {
            $genre = $this->findGenre($request->post('name'));
            $movie = $this->findMovie($request->post('movie'));
            $genre->removeMovie($movie);
        });

        return response('', Response::HTTP_OK);
    }

    /**
     * @SWG\Get(
     *     path="/genre",
     *     tags={"genre"},
     *     operationId="getGenre",
     *     summary="Retrieves a genre by name",
     *     description="",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Name of the genre",
     *         in="formData",
     *         name="name",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *         @SWG\Schema(ref="#/definitions/Genre")
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="resource not found"
     *     ),
     *     security={{"uma_auth":{"read:genres"}}}
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function show(Request $request)
    {
        $this->validate($request, ['name' => 'required|string']);
        $genre = $this->findGenre($request->post('name'));
        return response(json_encode($genre), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    /**
     * @SWG\Get(
     *     path="/genres",
     *     tags={"genre"},
     *     operationId="getGenres",
     *     summary="Retrieves all genres",
     *     description="",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Name of the genre",
     *         in="formData",
     *         name="name",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/Genre")
     *         ),
     *     ),
     *     security={{"uma_auth":{"read:genres"}}}
     * )
     *
     * @return Response
     */
    public function index()
    {
        $genres = $this->genreRepository->index();
        return response(json_encode($genres), Response::HTTP_OK, ['Content-Type' => 'application/json']);
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
}
