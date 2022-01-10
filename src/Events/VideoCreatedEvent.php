<?php

namespace App\Events;

//use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\Event;
//use Symfony\Contracts\EventDispatcher\Event;

class VideoCreatedEvent extends Event
{
    public function __construct($video)
    {
        $this->video = $video;
    }
}
