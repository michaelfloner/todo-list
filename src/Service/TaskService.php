<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\TaskCreateRequestDTO;
use App\DTO\TaskResponseDTO;
use App\DTO\TaskUpdateRequestDTO;
use App\Entity\Category;
use App\Entity\Task;
use App\Entity\User;
use App\Exception\ResponseException;
use App\Factory\TaskFactory;
use App\Flow\TaskTransition;
use App\Repository\CategoryRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Workflow\WorkflowInterface;

readonly class TaskService
{
    public function __construct(
        private TaskRepository $taskRepository,
        private CategoryRepository $categoryRepository,
        private EntityManagerInterface $entityManager,
        #[Target('task_state_workflow')]
        private WorkflowInterface $taskWorkflow,
    ) {
    }

    /**
     * @return array<TaskResponseDTO>
     */
    public function getListOfTaskByState(
        User $user,
        ?string $state,
    ): array {
        $tasks = $this->taskRepository->findByOwnerAndState($user, $state);

        return TaskResponseDTO::getFromList($tasks);
    }

    public function createTask(
        User $user,
        TaskCreateRequestDTO $dto
    ): Task {
        $categories = [];
        if ($dto->getCategories() !== null) {
            $categories = $this->findRequestCategories($user, $dto->getCategories());
        }

        $task = TaskFactory::create($dto, $user, $categories);
        $this->taskRepository->save($task);

        return $task;
    }

    public function updateTask(
        User $user,
        Task $task,
        TaskUpdateRequestDTO $dto,
    ): Task {
        $categories = [];
        if ($dto->getCategories() !== null) {
            $categories = $this->findRequestCategories($user, $dto->getCategories());
        }

        foreach ($task->getCategories() as $category) {
            $task->removeCategory($category);
        }

        $task
            ->setName($dto->getName())
            ->setDescription($dto->getDescription())
            ->addCategories($categories);

        $this->entityManager->flush();

        return $task;
    }

    public function deleteTask(
        Task $task,
    ): void {
        $this->transition($task, TaskTransition::DELETED);
    }

    public function transition(Task $task, string $transitionName): void
    {
        try {
            $this->taskWorkflow->apply($task, $transitionName);
        } catch (\LogicException $exception) {
            throw new ResponseException($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();
    }

    /**
     * @param array<int> $categoryIDs
     * @return array<Category>
     */
    private function findRequestCategories(
        User $user,
        array $categoryIDs,
    ): array {
        $categories = [];

        foreach ($categoryIDs as $id) {
            $category = $this->categoryRepository->findOneByIdAndOwner($user, $id);

            if ($category === null) {
                throw new ResponseException(sprintf("Category with id: %d not found", $id), Response::HTTP_BAD_REQUEST);
            }

            $categories[] = $category;
        }

        return $categories;
    }
}
