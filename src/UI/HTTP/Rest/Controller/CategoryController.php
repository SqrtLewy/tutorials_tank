<?php

namespace App\UI\HTTP\Rest\Controller;

use App\Application\Command\Category\ChangeName\ChangeNameCommand;
use App\Application\Command\Category\Create\CreateCategoryCommand;
use App\Application\Command\Category\Delete\DeleteCategoryCommand;
use App\Application\Query\Category\GetAll\GetAllCommand;
use App\Application\Query\Category\GetSingle\GetSingleCommand;
use App\Domain\Category\Exception\CategoryCreateException;
use App\Domain\Category\Exception\CategoryNameWasChangedException;
use App\Domain\Common\ValueObject\AggregateRootId;
use App\Infrastructure\Category\Repository\CategoryRepository;
use App\UI\HTTP\Common\Form\CategoryType;
use Broadway\EventHandling\EventBus;
use Broadway\EventStore\Dbal\DBALEventStore;
use League\Tactician\CommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CategoryController.
 */
class CategoryController extends Controller
{
    /**
     * @var CommandBus
     */
    private $queryBus;

    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var EventBus
     */
    private $eventBus;

    /**
     * @var DBALEventStore
     */
    private $eventStore;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(CommandBus $queryBus, CommandBus $commandBus, EventBus $eventBus, DBALEventStore $eventStore, CategoryRepository $categoryRepository)
    {
        $this->queryBus = $queryBus;
        $this->commandBus = $commandBus;
        $this->eventBus = $eventBus;
        $this->eventStore = $eventStore;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param Request $request
     *
     * @throws \Assert\AssertionFailedException
     *
     * @return Response
     */
    public function createCategoryAction(Request $request): Response
    {
        $command = new CreateCategoryCommand();
        $form = $this->createForm(CategoryType::class, $command);
        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->commandBus->handle($command);
            } catch (CategoryCreateException $exception) {
                $category = $this->categoryRepository->get(AggregateRootId::fromString($exception->getMessage()));
                $this->get('old_sound_rabbit_mq.projection_producer')->publish(serialize([
                    'test'
                ]));
                return new JsonResponse($category->serialize(), Response::HTTP_OK);
            }
        }

        return new Response($form->getErrors(), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param Request $request
     * @param string  $id
     *
     * @return Response
     *
     * @throws \Assert\AssertionFailedException
     */
    public function changeNameAction(Request $request, string $id): Response
    {
        $command = new ChangeNameCommand();
        $command->setId($id);
        $form = $this->createForm(CategoryType::class, $command);
        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->commandBus->handle($command);
            } catch (CategoryNameWasChangedException $exception) {
                $category = $this->categoryRepository->get(AggregateRootId::fromString($exception->getMessage()));

                return new JsonResponse($category->serialize(), Response::HTTP_OK);
            }
        }

        return new Response($form->getErrors(), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param Request $request
     * @param string  $id
     *
     * @throws \Assert\AssertionFailedException
     *
     * @return Response
     */
    public function deleteCategoryAction(Request $request, string $id): Response
    {
        $command = new DeleteCategoryCommand($id);
        $this->commandBus->handle($command);

        return new JsonResponse('', Response::HTTP_ACCEPTED);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function getAllCategoryAction(Request $request): Response
    {
        $page = $request->get('page') ?? 1;
        $limit = $request->get('limit') ?? 10;
        if ($request->get('query')) {
            $query = [
                'query' => [
                    'wildcard' => [
                        'name' => '*' . $request->get('query') . '*',
                    ],
                ],
            ];
        } else {
            $query = [];
        }

        $command = new GetAllCommand($page, $limit, $query);
        $model = $this->queryBus->handle($command);

        return new JsonResponse($model, 200);
    }

    /**
     * @param Request $request
     * @param string  $id
     *
     * @throws \Assert\AssertionFailedException
     *
     * @return Response
     */
    public function getSingleAction(Request $request, string $id): Response
    {
        $aggregateRootId = AggregateRootId::fromString($id);

        $command = new GetSingleCommand($aggregateRootId);
        $model = $this->queryBus->handle($command);

        return new JsonResponse($model, 200);
    }
}
