<?php
namespace App\Services;

Class MyService
{
    public function __construct(MySecondService $second_service, $globalParam)
    {
        dump($second_service);
        dump($globalParam);
    }
}
