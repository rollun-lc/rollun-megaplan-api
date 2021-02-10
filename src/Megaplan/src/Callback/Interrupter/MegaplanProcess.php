<?php

namespace rollun\api\megaplan\Callback\Interrupter;

use rollun\api\megaplan\Factory\DealFactoryAbstract;
use rollun\callback\Callback\Interrupter\InterrupterAbstract;
use \rollun\callback\Callback\Interrupter\Process as Component;


class MegaplanProcess
{
    protected $component;

    protected $factory;

    public function __construct(InterrupterAbstract $component, ?DealFactoryAbstract $factory = null)
    {
        $this->component = $component;
        $this->factory = $factory;
    }
    
    public function __invoke($value = null)
    {
        if ($this->factory) {
            $value = $this->factory->createInstance($value);
        }
        $payload = call_user_func([$this->component, '__invoke'], $value);
        $response = [];
        return $response;
    }
}