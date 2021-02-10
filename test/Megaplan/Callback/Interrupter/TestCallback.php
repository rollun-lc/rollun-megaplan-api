<?php


namespace rollun\test\api\megaplan\Callback\Interrupter;


use rollun\api\megaplan\Deals\DealAbstract;

class TestCallback
{
    public function __invoke(DealAbstract $deal)
    {
        file_put_contents('data/deal.json', $deal->toJson(true));
    }
}