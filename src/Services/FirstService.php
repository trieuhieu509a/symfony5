<?php

namespace App\Services;

class FirstService implements ServiceInterface
{
    public function __construct()
    {
        dump(__CLASS__);
    }
}
