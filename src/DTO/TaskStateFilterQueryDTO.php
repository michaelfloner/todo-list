<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\TaskState;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

readonly class TaskStateFilterQueryDTO
{
    public function __construct(
        #[Assert\Choice(choices: [
            TaskState::TODO,
            TaskState::IN_PROGRESS,
            TaskState::COMPLETED,
            TaskState::DELETED,
        ])]
        #[OA\Property(description: "Task state", type: "string", example: TaskState::TODO)]
        public ?string $state,
    ) {
    }
}
