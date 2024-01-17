<?php

declare(strict_types=1);

namespace App\DBAL;

use App\Entity\TaskState;

class EnumTaskStateType extends EnumType
{
    protected string $name = 'enumtaskstate';

    protected function getEnum(): string
    {
        return TaskState::class;
    }
}
