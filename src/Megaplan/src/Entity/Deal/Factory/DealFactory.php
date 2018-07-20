<?php

namespace rollun\api\megaplan\Entity\Deal\Factory;

use Interop\Container\ContainerInterface;
use rollun\api\megaplan\Entity\Deal\Deal;
use rollun\api\megaplan\Entity\Factory\AbstractFactory;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

class DealFactory extends AbstractDealFactory
{
    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     * @throws \Exception
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        parent::__invoke($container, $requestedName, $options);
        $config = $container->get('config');
        if (!isset($config[static::KEY][DealsFactory::DEALS_KEY])) {
            throw new ServiceNotFoundException("The configuration for deals entity is not found");
        }
        $serviceConfig = $config[static::KEY][DealsFactory::DEALS_KEY];

        $programId = isset($serviceConfig[static::FILTER_FIELD_KEY][static::FILTER_FIELD_PROGRAM_KEY])
            ? $serviceConfig[static::FILTER_FIELD_KEY][static::FILTER_FIELD_PROGRAM_KEY] : null;
        $requestedFields = isset($serviceConfig[static::REQUESTED_FIELDS_KEY]) ? $serviceConfig[static::REQUESTED_FIELDS_KEY] : [];
        $extraFields = isset($serviceConfig[static::EXTRA_FIELDS_KEY]) ? $serviceConfig[static::EXTRA_FIELDS_KEY] : [];
        $dealListFields = $container->get($serviceConfig[static::LIST_FIELDS_KEY]);

        $instance = new Deal($dealListFields, $programId, $requestedFields, $extraFields);
        return $instance;
    }
}