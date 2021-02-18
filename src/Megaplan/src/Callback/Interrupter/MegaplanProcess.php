<?php

namespace rollun\api\megaplan\Callback\Interrupter;

use rollun\api\megaplan\Factory\DealFactoryAbstract;
use rollun\callback\Callback\Interrupter\InterrupterAbstract;
use \rollun\callback\Callback\Interrupter\Process as Component;


class MegaplanProcess
{
    protected $component;

    public function __construct(InterrupterAbstract $component)
    {
        $this->component = $component;
    }
    
    public function __invoke($value = null)
    {
        $payload = call_user_func([$this->component, '__invoke'], $value);
        $response = [];
        return $response;
    }
}