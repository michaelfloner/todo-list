<?php

declare(strict_types=1);

namespace App\Flow;

class TaskTransition
{
    public const IN_PROGRESS = 'task_transition_in_progress';
    public const COMPLETED = 'task_transition_completed';
    public const DELETED = 'task_transition_deleted';
}
