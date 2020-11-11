<?php

namespace App\EventListener;

use App\Entity\Task;
use App\Event\TaskCreatedEvent;
use App\Event\TaskDeletedEvent;
use App\Event\TaskUpdatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TaskSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            TaskCreatedEvent::NAME => 'onTaskCreated',
            TaskUpdatedEvent::NAME => 'onTaskUpdated',
            TaskDeletedEvent::NAME => 'onTaskDeleted',
        ];
    }

    public function onTaskCreated(TaskCreatedEvent $event)
    {
        $task = $event->getTask();
        $this->handleTaskAction($task);
    }

    public function onTaskUpdated(TaskUpdatedEvent $event)
    {
        $task = $event->getTask();
        $this->handleTaskAction($task);
    }

    public function onTaskDeleted(TaskDeletedEvent $event)
    {
        $task = $event->getTask();
        $this->handleTaskAction($task);
    }

    /**
     * @param Task $task
     */
    protected function handleTaskAction(Task $task): void
    {
        $project = $task->getProject();
        $allTasks = $project->getTasks();

        $duration = 0;
        foreach ($allTasks as $task) {
            if ($task->getDeleted()) {
                continue;
            }

            $duration += $task->getDuration();
        }

        $project->setDuration($duration);
        $incompleteFlag = false;

        foreach ($allTasks as $task) {
            if ($task->getDeleted()) {
                continue;
            }

            if ($task->getStatus() === 'failed') {
                $project->setStatus('failed');
                $this->entityManager->persist($project);
                $this->entityManager->flush();
                return;
            } elseif ($task->getStatus() !== 'complete') {
                $incompleteFlag = true;
            }
        }

        if ($incompleteFlag) {
            $project->setStatus('pending');
        } else {
            $project->setStatus('complete');
        }

        $this->entityManager->persist($project);
        $this->entityManager->flush();
    }
}