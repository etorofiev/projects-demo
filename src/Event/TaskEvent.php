<?php

namespace App\Event;

use App\Entity\Task;
use Symfony\Contracts\EventDispatcher\Event;

class TaskEvent extends Event
{
    protected $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function getTask()
    {
        return $this->task;
    }
}