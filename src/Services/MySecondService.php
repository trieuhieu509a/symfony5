<?php

namespace App\Services;

class MySecondService
{
    public function __construct()
    {
        dump('from second service');
        $this->doSomeThings();
    }

    public function someMethod()
    {
        return __METHOD__;
    }

    public function doSomeThings()
    {
        return dump(__METHOD__);
    }

    public function doSomeThings2()
    {
        return __METHOD__;
    }
}
