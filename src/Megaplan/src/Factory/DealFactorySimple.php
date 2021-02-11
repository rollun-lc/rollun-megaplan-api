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
class DealFactorySimple extends DealFactoryAbstract
{
    protected $dealClass = DealSimple::class;

    public function __construct($dealClass)
    {
        $this->dealClass = $dealClass;
    }

    public function createDeal($data): DealAbstract
    {
        $deal = $data['data']['deal'];

        return new $this->dealClass($deal);
    }
}