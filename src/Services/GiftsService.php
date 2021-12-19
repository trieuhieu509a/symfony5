<?php
namespace App\Services;

use Psr\Log\LoggerInterface;

Class GiftsService {
    public $gifts = ['flowers', 'car', 'piano', 'money'];
    public function __construct(LoggerInterface $logger)
    {
        $logger->info('Gifts were random');
        shuffle($this->gifts);
    }
}