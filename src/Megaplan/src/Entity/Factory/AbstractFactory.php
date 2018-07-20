<?php

namespace rollun\api\megaplan\Entity\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class AbstractFactory implements FactoryInterface
{
    const KEY = 'megaplan_entities';

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        // There is no requested service section in the config
        if (!isset($config[static::KEY])) {
            throw new ServiceNotFoundException("There is no section for megaplan entities in the project config");
        }
    }
}