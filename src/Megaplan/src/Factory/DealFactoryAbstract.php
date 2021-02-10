<?php


namespace rollun\api\megaplan\Factory;


use rollun\api\megaplan\Deals\DealAbstract;
use rollun\api\megaplan\Deals\DealSimple;

/**
 * Class DealFactoryAbstract
 * @package rollun\api\megaplan\Factory
 *
 * @todo
 */
class DealFactoryAbstract
{
    public const KEY_DEAL = 'deal';

    protected $dealClass = DealSimple::class;

    public function __construct($dealClass)
    {
        $this->dealClass = $dealClass;
    }

    public function createInstance($data): DealAbstract
    {
        $deal = $data['data']['deal'];

        return new $this->dealClass($deal);
    }
}