<?php

namespace App\Services;

class MyService
{
    public $my;
    public $logger;

    public function __construct($service)
    {
        dump('hi!');
//        dump($service);
//        $this->secondService = $service;
    }

//    /**
//     * @param MySecondService $second_service
//     * @required // anotation force call method
//     */
//    public function setSecondService(MySecondService $second_service)
//    {
//        dump($second_service);
//    }

    public function someAction(){
//        dump($this->service->doSomeThings2());
        dump($this->my);
        dump($this->logger);
    }
}
