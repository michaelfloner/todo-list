<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Task;
use App\Entity\TaskState;
use Doctrine\Common\Collections\ArrayCollection;
use OpenApi\Attributes as OA;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Reference;

class TaskResponseDTO
{
    /**
     * @param array<CategoryResponseDTO> $categories
     */
    public function __construct(
        #[OA\Property(description: "Task id", type: "integer", example: 1)]
        public int|null $id,
        #[OA\Property(description: "Task name", type: "string", example: "task 1")]
        public string $name,
        #[OA\Property(
            type: "string",
            enum: [TaskState::TODO, TaskState::IN_PROGRESS, TaskState::COMPLETED, TaskState::DELETED],
            example: TaskState::TODO->value
        )
        ]
        public TaskState $state,
        #[OA\Property(description: "Task description", type: "string", example: "task xxxx", nullable: true)]
        public string|null $description,
        #[OA\Property(type: "array", items: new OA\Items(ref: '#/components/schemas/CategoryResponseDTO'))]
        public array $categories = []
    ) {
    }

    public static function create(Task $task): self
    {
        $categories = CategoryResponseDTO::createFromList($task->getCategories());

        return new self(
            $task->getId(),
            $task->getName(),
            $task->getState(),
            $task->getDescription(),
            $categories,
        );
    }

    /**
     * @param array<Task> $tasks
     * @return array<TaskResponseDTO>
     */
    public static function getFromList(array $tasks): array
    {
        $result = new ArrayCollection();

        foreach ($tasks as $task) {
            if (!$task instanceof Task) {
                continue;
            }

            $result->add(
                self::create($task)
            );
        }

        return $result->toArray();
    }
}
