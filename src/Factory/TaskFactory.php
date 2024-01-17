<?php

declare(strict_types=1);

namespace App\Factory;

use App\DTO\TaskCreateRequestDTO;
use App\Entity\Category;
use App\Entity\Task;
use App\Entity\TaskState;
use App\Entity\User;

class TaskFactory
{
    /**
     * @param array<Category> $categories
     */
    public static function create(
        TaskCreateRequestDTO $dto,
        User $owner,
        array $categories = []
    ): Task {
        $task = new Task();

        $task
            ->setState(TaskState::TODO)
            ->setOwner($owner)
            ->setName($dto->getName())
            ->setDescription($dto->getDescription())
            ->addCategories($categories);

        return $task;
    }
}
