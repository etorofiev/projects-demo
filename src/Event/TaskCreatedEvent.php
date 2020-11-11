<?php

namespace App\Event;

class TaskCreatedEvent extends TaskEvent
{
    public const NAME = 'task.created';
}