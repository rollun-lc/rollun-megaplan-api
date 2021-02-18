<?php


namespace rollun\api\megaplan\Factory;


use rollun\api\megaplan\Deals\DealAbstract;
use rollun\api\megaplan\Deals\DealInterface;
use rollun\api\megaplan\Deals\DealSimple;

/**
 * Class DealFactoryAbstract
 * @package rollun\api\megaplan\Factory
 *
 * @todo
 */
class EntityFactorySimple extends DealFactoryAbstract
{
    protected $dealClass = DealSimple::class;

    public function __construct($dealClass)
    {
        $this->dealClass = $dealClass;
    }

    public function createDeal($data): DealInterface
    {
        $deal = $data['data']['deal'];

        return new $this->dealClass($deal);
    }
}