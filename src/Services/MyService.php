<?php
namespace App\Services;

Class MyService
{
    public function __construct(MySecondService $second_service)
    {
        dump($second_service);
    }
}
