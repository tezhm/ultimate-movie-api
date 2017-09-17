<?php declare(strict_types=1);
namespace Uma\Infrastructure\Http\Controllers\Actor;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Uma\Domain\Exceptions\DomainException;
use Uma\Domain\Model\Actor;
use Uma\Domain\Model\ActorRepository;
use Uma\Infrastructure\Http\Controllers\Controller;

/**
 * Handles commands and queries for Actors.
 *
 * @package Uma\Infrastructure\Http\Controllers\Actor
 */
class ActorController extends Controller
{
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var ActorRepository */
    private $actorRepository;

    /**
     * Create a new actor controller instance.
     *
     * @param EntityManagerInterface $entityManager
     * @param ActorRepository $actorRepository
     */
    public function __construct(EntityManagerInterface $entityManager, ActorRepository $actorRepository)
    {
        $this->entityManager = $entityManager;
        $this->actorRepository = $actorRepository;
    }

    /**
     * TODO: swagger doc here I think
     *
     * @param Request $request
     */
    public function create(Request $request)
    {
        $this->validate($request, ['name' => 'required|string', 'birth' => 'required|date']);

        $this->entityManager->transactional(function() use($request)
        {
            $actor = new Actor(
                $request->post('name'),
                new DateTime($request->post('birth'))
            );
            $this->actorRepository->add($actor);
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
            $actor = $this->actorRepository->showByName($request->post('name'));

            if ($actor === null)
            {
                throw new DomainException('Actor does not exist');
            }

            $this->actorRepository->remove($actor);
        });
    }

    /**
     * TODO: swagger doc here I think
     *
     * @param Request $request
     */
    public function change(Request $request)
    {
        $this->validate($request, ['name' => 'required|string', 'birth' => 'date', 'bio' => 'string', 'image' => 'string']);

        $this->entityManager->transactional(function() use($request)
        {
            $actor = $this->actorRepository->showByName($request->post('name'));

            if ($actor === null)
            {
                throw new DomainException('Actor does not exist');
            }

            if ($request->has('birth'))
            {
                $actor->setBirth(new DateTime($request->post('birth')));
            }

            if ($request->has('bio'))
            {
                $actor->setBio($request->post('bio'));
            }

            if ($request->has('image'))
            {
                $actor->setImage($request->post('image'));
            }
        });
    }

    /**
     * Queries an Actor by name.
     *
     * @param Request $request
     * @return Response
     */
    public function show(Request $request)
    {
        $this->validate($request, ['name' => 'required|string']);
        $actor = $this->actorRepository->showByName($request->post('name'));

        if ($actor === null)
        {
            throw new DomainException('Actor does not exist');
        }

        return response(json_encode($actor), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    /**
     * TODO: swagger documentation
     */
    public function index()
    {
        $actors = $this->actorRepository->index();
        return response(json_encode($actors), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}
