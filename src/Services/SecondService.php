<?php

namespace App\Services;

class SecondService implements ServiceInterface
{
    public function __construct()
    {
        dump(__CLASS__);
    }
}
