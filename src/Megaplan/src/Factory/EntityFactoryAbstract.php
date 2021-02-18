<?php


namespace rollun\api\megaplan\Factory;


use rollun\api\megaplan\Entity\EntityInterface;

abstract class EntityFactoryAbstract
{
    public function createInstance($data): EntityInterface
    {
        return $this->createConcrete($data);
    }

    protected abstract function createConcrete($data): EntityInterface;
}