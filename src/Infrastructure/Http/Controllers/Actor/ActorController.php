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
     * @SWG\Post(
     *     path="/actor",
     *     tags={"actor"},
     *     operationId="createActor",
     *     summary="Creates a new actor",
     *     description="",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Full name of the actor",
     *         in="formData",
     *         name="name",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="Birth date of the actor",
     *         in="formData",
     *         name="birth",
     *         required=true,
     *         type="string",
     *         format="date",
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     security={{"uma_auth":{"write:actors", "read:actors"}}}
     * )
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
     * @SWG\Delete(
     *     path="/actor",
     *     tags={"actor"},
     *     operationId="removeActor",
     *     summary="Removes an existing actor",
     *     description="",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Full name of the actor",
     *         in="formData",
     *         name="name",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     security={{"uma_auth":{"write:actors", "read:actors"}}}
     * )
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
     * @SWG\Put(
     *     path="/actor",
     *     tags={"actor"},
     *     operationId="changeActor",
     *     summary="Updates an existing actor",
     *     description="Provides functionality for update birth, bio, and image.",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Full name of the actor",
     *         in="formData",
     *         name="name",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="Full name of the actor",
     *         in="formData",
     *         name="birth",
     *         required=false,
     *         type="string",
     *         format="date",
     *     ),
     *     @SWG\Parameter(
     *         description="Full name of the actor",
     *         in="formData",
     *         name="bio",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="Full name of the actor",
     *         in="formData",
     *         name="image",
     *         required=false,
     *         type="string",
     *         format="byte",
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     security={{"uma_auth":{"write:actors", "read:actors"}}}
     * )
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
     * @SWG\Get(
     *     path="/actor/showByName",
     *     tags={"actor"},
     *     operationId="changeActor",
     *     summary="Removes an existing actor",
     *     description="",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Full name of the actor",
     *         in="formData",
     *         name="name",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     security={{"uma_auth":{"write:actors", "read:actors"}}}
     * )
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
     * @SWG\Get(
     *     path="/actor/index",
     *     tags={"actor"},
     *     operationId="changeActor",
     *     summary="Removes an existing actor",
     *     description="",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Full name of the actor",
     *         in="formData",
     *         name="name",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     security={{"uma_auth":{"write:actors", "read:actors"}}}
     * )
     */
    public function index()
    {
        $actors = $this->actorRepository->index();
        return response(json_encode($actors), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}
