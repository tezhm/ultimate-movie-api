<?php declare(strict_types=1);
namespace Uma\Infrastructure\Http\Controllers\Movie;

use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Uma\Domain\Exceptions\DomainException;
use Uma\Domain\Model\ActorRepository;
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
     * TODO: swagger doc here I think
     *
     * @param Request $request
     */
    public function create(Request $request)
    {
        $this->validate($request, ['name' => 'required|string']);

        $this->entityManager->transactional(function() use($request)
        {
            $movie = new Movie($request->post('name'));
            $this->movieRepository->add($movie);
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
            $movie = $this->movieRepository->showByName($request->post('name'));

            if ($movie === null)
            {
                throw new DomainException('Movie does not exist');
            }

            $this->movieRepository->remove($movie);
        });
    }

    /**
     * TODO: swagger doc here I think
     *
     * @param Request $request
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
            $movie = $this->movieRepository->showByName($request->post('name'));

            if ($movie === null)
            {
                throw new DomainException('Movie does not exist');
            }

            if ($request->has('genre'))
            {
                $genre = $this->genreRepository->showByName($request->post('genre'));

                if ($genre === null)
                {
                    throw new DomainException('Genre does not exist');
                }

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
    }

    /**
     * TODO: swagger doc here I think
     *
     * @param Request $request
     */
    public function addActor(Request $request)
    {
        $rules = ['name' => 'required|string', 'actor' => 'required|string', 'character' => 'required|string'];
        $this->validate($request, $rules);

        $this->entityManager->transactional(function() use($request)
        {
            $movie = $this->movieRepository->showByName($request->post('name'));

            if ($movie === null)
            {
                throw new DomainException('Movie does not exist');
            }

            $actor = $this->actorRepository->showByName($request->post('actor'));

            if ($actor === null)
            {
                throw new DomainException('Actor does not exist');
            }

            $movie->addActor($request->post('character'), $actor);
        });
    }

    /**
     * TODO: swagger doc here I think
     *
     * @param Request $request
     */
    public function removeActor(Request $request)
    {
        $rules = ['name' => 'required|string', 'actor' => 'required|string'];
        $this->validate($request, $rules);

        $this->entityManager->transactional(function() use($request)
        {
            $movie = $this->movieRepository->showByName($request->post('name'));

            if ($movie === null)
            {
                throw new DomainException('Movie does not exist');
            }

            $actor = $this->actorRepository->showByName($request->post('actor'));

            if ($actor === null)
            {
                throw new DomainException('Actor does not exist');
            }

            $movie->removeActor($actor);
        });
    }

    /**
     * Queries an Movie by name.
     *
     * @param Request $request
     * @return Response
     */
    public function show(Request $request)
    {
        $this->validate($request, ['name' => 'required|string']);
        $genre = $this->movieRepository->showByName($request->post('name'));

        if ($genre === null)
        {
            throw new DomainException('Movie does not exist');
        }

        return response(json_encode($genre), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}
