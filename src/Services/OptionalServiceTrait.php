<?php

namespace App\Services;

trait OptionalServiceTrait
{
    /**
     * @var MySecondService $service
     */
    private $service;

    /**
     * @param MySecondService $second_service
     * @required // anotation force call method
     */
    public function setSecondService(MySecondService $second_service)
    {
        $this->service = $second_service;
    }
}
