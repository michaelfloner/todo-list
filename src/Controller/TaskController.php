<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\DTO\TaskCreateRequestDTO;
use App\DTO\TaskResponseDTO;
use App\DTO\TaskUpdateRequestDTO;
use App\Entity\Task;
use App\Entity\TaskState;
use App\Entity\User;
use App\Exception\ResponseException;
use App\Flow\TaskTransition;
use App\Service\TaskService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use OpenApi\Attributes as OA;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/task', name: "task:")]
class TaskController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private readonly TaskService $taskService,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('', name: 'create', methods: 'POST', format: 'json')]
    #[OA\Tag("Task")]
    #[OA\RequestBody(
        description: 'Task request',
        content: new Model(type: TaskCreateRequestDTO::class),
    )]
    #[OA\Response(
        response: 201,
        description: 'Successful response',
        content: new Model(type: TaskResponseDTO::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'Returned when invalid data posted'
    )]
    #[OA\Response(
        response: 401,
        description: 'Invalid JWT Token'
    )]
    #[OA\Response(
        response: 403,
        description: 'Unauthorized'
    )]
    #[OA\Response(
        response: 500,
        description: 'Internal error'
    )]
    public function createTask(
        #[CurrentUser] User $user,
        #[MapRequestPayload(
            acceptFormat: 'json',
            validationFailedStatusCode: Response::HTTP_BAD_REQUEST
        )] TaskCreateRequestDTO $dto,
    ): JsonResponse {
        try {
            $task = $this->taskService->createTask($user, $dto);
        } catch (\Throwable $exception) {
            throw new ResponseException($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(TaskResponseDTO::create($task), Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'get', requirements: ['id' => '\d+'], methods: 'GET')]
    #[OA\Parameter(
        name: 'id',
        description: 'ID entity',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )
    ]
    #[OA\Tag("Task")]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: TaskResponseDTO::class)
    )]
    #[OA\Response(
        response: 401,
        description: 'Invalid JWT Token'
    )]
    #[OA\Response(
        response: 403,
        description: 'Unauthorized'
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found'
    )]
    #[IsGranted('view', 'task', 'Not found', Response::HTTP_NOT_FOUND)]
    public function getTask(
        Task $task,
    ): JsonResponse {
        return $this->JSON(TaskResponseDTO::create($task), Response::HTTP_OK);
    }

    #[Route('', name: 'get:by-state', methods: 'GET')]
    #[OA\Tag("Task")]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: TaskResponseDTO::class)
    )]
    #[OA\Response(
        response: 401,
        description: 'Invalid JWT Token'
    )]
    #[OA\Response(
        response: 403,
        description: 'Unauthorized'
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found'
    )]
    public function getTaskByState(
        #[CurrentUser] User $user,
        #[MapQueryParameter,
    OA\QueryParameter(name: 'state', required: false, schema: new OA\Schema(enum: TaskState::class))]
        ?string $state
    ): JsonResponse {
        $data = $this->taskService->getListOfTaskByState($user, $state);

        return $this->JSON($data, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'edit', requirements: ['id' => '\d+'], methods: 'PUT', format: 'json')]
    #[OA\Parameter(
        name: 'id',
        description: 'ID entity',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )
    ]
    #[OA\Tag("Task")]
    #[OA\RequestBody(
        description: 'Task update request',
        content: new Model(type: TaskUpdateRequestDTO::class),
    )]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: TaskResponseDTO::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'Returned when invalid data posted'
    )]
    #[OA\Response(
        response: 401,
        description: 'Invalid JWT Token'
    )]
    #[OA\Response(
        response: 403,
        description: 'Unauthorized'
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found'
    )]
    #[IsGranted('edit', 'task', 'Not found', Response::HTTP_NOT_FOUND)]
    public function updateTask(
        #[CurrentUser] User $user,
        Task $task,
        #[MapRequestPayload(
            acceptFormat: 'json',
            validationFailedStatusCode: Response::HTTP_BAD_REQUEST
        )] TaskUpdateRequestDTO $dto,
    ): JsonResponse {
        $task = $this->taskService->updateTask($user, $task, $dto);

        return $this->json(TaskResponseDTO::create($task), Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'delete', requirements: ['id' => '\d+'], methods: 'DELETE')]
    #[OA\Parameter(
        name: 'id',
        description: 'ID entity',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )
    ]
    #[OA\Tag("Task")]
    #[OA\Response(
        response: 204,
        description: 'Successful response'
    )]
    #[OA\Response(
        response: 401,
        description: 'Invalid JWT Token'
    )]
    #[OA\Response(
        response: 403,
        description: 'Unauthorized'
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found'
    )]
    #[IsGranted('delete', 'task', 'Not found', Response::HTTP_NOT_FOUND)]
    public function deleteTask(
        Task $task,
    ): JsonResponse {
        $this->taskService->deleteTask($task);

        return $this->emptyResponse();
    }

    #[Route('/{id}/in-progress', name: 'in-progress', requirements: ['id' => '\d+'], methods: 'PATCH')]
    #[OA\Parameter(
        name: 'id',
        description: 'ID entity',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )
    ]
    #[OA\Tag("Task")]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: TaskResponseDTO::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad request'
    )]
    #[OA\Response(
        response: 401,
        description: 'Invalid JWT Token'
    )]
    #[OA\Response(
        response: 403,
        description: 'Unauthorized'
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found'
    )]
    #[IsGranted('edit', 'task', 'Not found', Response::HTTP_NOT_FOUND)]
    public function taskToInProgress(
        Task $task,
    ): JsonResponse {
        $this->taskService->transition($task, TaskTransition::IN_PROGRESS);
        $this->entityManager->refresh($task);

        return $this->json(TaskResponseDTO::create($task), Response::HTTP_OK);
    }

    #[Route('/{id}/completed', name: 'completed', requirements: ['id' => '\d+'], methods: 'PATCH')]
    #[OA\Parameter(
        name: 'id',
        description: 'ID entity',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )
    ]
    #[OA\Tag("Task")]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: TaskResponseDTO::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad request'
    )]
    #[OA\Response(
        response: 401,
        description: 'Invalid JWT Token'
    )]
    #[OA\Response(
        response: 403,
        description: 'Unauthorized'
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found'
    )]
    #[IsGranted('edit', 'task', 'Not found', Response::HTTP_NOT_FOUND)]
    public function taskCompleted(
        Task $task,
    ): JsonResponse {
        $this->taskService->transition($task, TaskTransition::COMPLETED);
        $this->entityManager->refresh($task);

        return $this->json(TaskResponseDTO::create($task), Response::HTTP_OK);
    }
}
