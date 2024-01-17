<?php

declare(strict_types=1);

namespace App\Entity;

enum TaskState: string
{
    case TODO = 'todo';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case DELETED = 'deleted';
}
