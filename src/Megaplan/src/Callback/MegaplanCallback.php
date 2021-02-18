<?php


namespace rollun\api\megaplan\Callback;


use rollun\api\megaplan\Factory\EntityFactoryAbstract;
use rollun\api\megaplan\MegaplanClient;

class MegaplanCallback
{
    protected $callback;

    protected $factory;

    public function __construct(callable $callback, EntityFactoryAbstract $factory)
    {
        $this->callback = $callback;
        $this->factory = $factory;
    }

    public function __invoke($value = null)
    {
        $entity = $this->factory->createInstance($value);

        ($this->callback)($entity);
    }
}