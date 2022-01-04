<?php

namespace App\Services;

class MyService
{
    use OptionalServiceTrait;

    public function __construct()
    {
        //dump('');
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
        dump($this->service->doSomeThings2());
    }
}
