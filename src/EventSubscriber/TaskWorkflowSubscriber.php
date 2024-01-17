<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Task;
use App\Flow\TaskTransition;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

readonly class TaskWorkflowSubscriber implements EventSubscriberInterface
{
    public function onComplete(Event $event): void
    {
        $task = $event->getSubject();

        if (
            $task instanceof Task &&
            $event->getTransition() !== null &&
            $event->getTransition()->getName() === TaskTransition::COMPLETED
        ) {
            $task->setCompletedAt(new \DateTimeImmutable());
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.task_state_workflow.completed' => 'onComplete'
        ];
    }
}
