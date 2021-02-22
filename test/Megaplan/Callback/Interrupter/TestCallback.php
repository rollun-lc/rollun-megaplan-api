<?php


namespace rollun\test\api\megaplan\Callback\Interrupter;


use rollun\api\megaplan\Entity\EntityInterface;

class TestCallback
{
    public function __invoke(EntityInterface $deal)
    {
        file_put_contents('data/deal.json', $deal->toJson(true));
    }
}