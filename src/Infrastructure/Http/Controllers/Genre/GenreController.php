<?php declare(strict_types=1);
namespace Uma\Infrastructure\Http\Controllers\Genre;

use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Uma\Domain\Exceptions\DomainException;
use Uma\Domain\Model\ActorRepository;
use Uma\Domain\Model\Genre;
use Uma\Domain\Model\GenreRepository;
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
     * TODO: swagger doc here I think
     *
     * @param Request $request
     */
    public function create(Request $request)
    {
        $this->validate($request, ['name' => 'required|string']);

        $this->entityManager->transactional(function() use($request)
        {
            $genre = new Genre($request->post('name'));
            $this->genreRepository->add($genre);
        });
    }

    /**
     * TODO: swagger doc here I think
     *
     * @param Request $request
     */
    public function remove(Request $request)
    {
        $this->validate($request, ['name' => 'required|string']);

        $this->entityManager->transactional(function() use($request)
        {
            $genre = $this->genreRepository->showByName($request->post('name'));

            if ($genre === null)
            {
                throw new DomainException('Genre does not exist');
            }

            $this->genreRepository->remove($genre);
        });
    }

    /**
     * TODO: swagger doc here I think
     *
     * @param Request $request
     */
    public function addActor(Request $request)
    {
        $this->validate($request, ['name' => 'required|string', 'actor' => 'required|string']);

        $this->entityManager->transactional(function() use($request)
        {
            $genre = $this->genreRepository->showByName($request->post('name'));

            if ($genre === null)
            {
                throw new DomainException('Genre does not exist');
            }

            $actor = $this->actorRepository->showByName($request->post('actor'));

            if ($actor === null)
            {
                throw new DomainException('Actor does not exist');
            }

            $genre->addActor($actor);
        });
    }

    /**
     * TODO: swagger doc here I think
     *
     * @param Request $request
     */
    public function addMovie(Request $request)
    {
        $this->validate($request, ['name' => 'required|string', 'movie' => 'required|string']);

        $this->entityManager->transactional(function() use($request)
        {
            $genre = $this->genreRepository->showByName($request->post('name'));

            if ($genre === null)
            {
                throw new DomainException('Genre does not exist');
            }

            $movie = $this->movieRepository->showByName($request->post('movie'));

            if ($movie === null)
            {
                throw new DomainException('Movie does not exist');
            }

            $genre->addMovie($movie);
        });
    }

    /**
     * TODO: swagger doc here I think
     *
     * @param Request $request
     */
    public function removeActor(Request $request)
    {
        $this->validate($request, ['name' => 'required|string', 'actor' => 'required|string']);

        $this->entityManager->transactional(function() use($request)
        {
            $genre = $this->genreRepository->showByName($request->post('name'));

            if ($genre === null)
            {
                throw new DomainException('Genre does not exist');
            }

            $actor = $this->actorRepository->showByName($request->post('actor'));

            if ($actor === null)
            {
                throw new DomainException('Actor does not exist');
            }

            $genre->removeActor($actor);
        });
    }

    /**
     * TODO: swagger doc here I think
     *
     * @param Request $request
     */
    public function removeMovie(Request $request)
    {
        $this->validate($request, ['name' => 'required|string', 'movie' => 'required|string']);

        $this->entityManager->transactional(function() use($request)
        {
            $genre = $this->genreRepository->showByName($request->post('name'));

            if ($genre === null)
            {
                throw new DomainException('Genre does not exist');
            }

            $movie = $this->movieRepository->showByName($request->post('movie'));

            if ($movie === null)
            {
                throw new DomainException('Movie does not exist');
            }

            $genre->removeMovie($movie);
        });
    }

    /**
     * Queries an Genre by name.
     *
     * @param Request $request
     * @return Response
     */
    public function show(Request $request)
    {
        $this->validate($request, ['name' => 'required|string']);
        $genre = $this->genreRepository->showByName($request->post('name'));

        if ($genre === null)
        {
            throw new DomainException('Genre does not exist');
        }

        return response(json_encode($genre), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}
