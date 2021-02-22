<?php


namespace rollun\api\megaplan\Factory;


use rollun\api\megaplan\Entity\EntityAbstract;
use rollun\api\megaplan\Entity\EntityInterface;
use rollun\api\megaplan\Entity\EntitySimple;

/**
 * Class DealFactoryAbstract
 * @package rollun\api\megaplan\Factory
 *
 * @todo
 */
class EntityFactorySimple extends EntityFactoryAbstract
{
    protected $dealClass = EntitySimple::class;

    public function __construct($dealClass)
    {
        $this->dealClass = $dealClass;
    }

    public function createConcrete($data): EntityInterface
    {
        $deal = $data['data']['deal'];

        return new $this->dealClass($deal);
    }
}