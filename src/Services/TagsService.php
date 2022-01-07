<?php

namespace App\Services;

use Doctrine\ORM\Event\PostFlushEventArgs;

class TagsService
{
    public function __construct()
    {
        dump(__CLASS__);
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        dump('hello post Flush');
        dump($args);
    }

    public function clear()
    {
        dump('hello ... clear');
    }
}
