<?php

namespace App\Event;

class TaskDeletedEvent extends TaskEvent
{
    public const NAME = 'task.deleted';
}