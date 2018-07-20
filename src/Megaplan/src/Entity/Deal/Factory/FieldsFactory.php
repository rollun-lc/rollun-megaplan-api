<?php

namespace rollun\api\megaplan\Entity\Deal\Factory;

use Interop\Container\ContainerInterface;
use rollun\api\megaplan\Entity\Deal\Fields;
use rollun\api\megaplan\Entity\Factory\AbstractFactory;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use rollun\api\megaplan\Exception\InvalidArgumentException;

class FieldsFactory extends AbstractFactory
{
    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        parent::__invoke($container, $requestedName, $options);
        $config = $container->get('config');
        if (!isset($config[static::KEY][DealsFactory::DEALS_KEY])) {
            throw new ServiceNotFoundException("The configuration for deals entity is not found");
        }
        $serviceConfig = $config[static::KEY][DealsFactory::DEALS_KEY];
        if (!isset($serviceConfig[DealsFactory::FILTER_FIELD_KEY][DealsFactory::FILTER_FIELD_PROGRAM_KEY])) {
            throw new InvalidArgumentException("Fields entity has to receive necessary parameter \""
                . DealsFactory::FILTER_FIELD_PROGRAM_KEY . "\" in the \"" . DealsFactory::FILTER_FIELD_KEY . "\" array");
        }
        $instance = new Fields($serviceConfig[DealsFactory::FILTER_FIELD_KEY][DealsFactory::FILTER_FIELD_PROGRAM_KEY]);
        return $instance;
    }
}