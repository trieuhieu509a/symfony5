<?php

namespace App\Services;

class MySecondService
{
    public function __construct()
    {
        dump('from second service');
        $this->doSomeThings();
    }

    public function doSomeThings()
    {

    }

    public function doSomeThings2()
    {
        return __METHOD__;
    }
}
