<?php


namespace rollun\api\megaplan\Factory;


use rollun\api\megaplan\Deals\DealAbstract;

abstract class DealFactoryAbstract
{
    public function createInstance($data): DealAbstract
    {
        return $this->createDeal($data);
    }

    protected abstract function createDeal($data): DealAbstract;
}