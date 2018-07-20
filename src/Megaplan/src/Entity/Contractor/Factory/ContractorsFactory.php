<?php

namespace rollun\api\megaplan\Entity\Contractor\Factory;

use Interop\Container\ContainerInterface;
use rollun\api\megaplan\Entity\Contractor\Contractors;
use rollun\api\megaplan\Entity\Deal\Deals;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use rollun\api\megaplan\Entity\Factory\AbstractFactory;
use rollun\api\megaplan\Exception\InvalidArgumentException;

class ContractorsFactory extends AbstractContractorFactory
{
    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     * @throws InvalidArgumentException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        parent::__invoke($container, $requestedName, $options);
        $config = $container->get('config');
        if (!isset($config[static::KEY][self::DEALS_KEY])) {
            throw new ServiceNotFoundException("The configuration for deals entity is not found");
        }
        $serviceConfig = $config[static::KEY][self::DEALS_KEY];
        if (!isset($serviceConfig[static::LIST_FIELDS_KEY])) {
            throw new InvalidArgumentException("Requested parameter \""
                . static::LIST_FIELDS_KEY . "\" is not found in the entity config");
        }
        if (!isset($serviceConfig[static::FILTER_FIELD_KEY][static::FILTER_FIELD_PROGRAM_KEY])) {
            throw new InvalidArgumentException("Deal entity has to receive necessary parameter \""
                . static::FILTER_FIELD_PROGRAM_KEY . "\" in the \"" . static::FILTER_FIELD_KEY . "\" array");
        }

        $dealListFields = $container->get($serviceConfig[static::LIST_FIELDS_KEY]);
        $filterFields = isset($serviceConfig[static::FILTER_FIELD_KEY]) ? $serviceConfig[static::FILTER_FIELD_KEY] : [];
        $requestedFields = isset($serviceConfig[static::REQUESTED_FIELDS_KEY]) ? $serviceConfig[static::REQUESTED_FIELDS_KEY] : [];
        $extraFields = isset($serviceConfig[static::EXTRA_FIELDS_KEY]) ? $serviceConfig[static::EXTRA_FIELDS_KEY] : [];

        $instance = new Contractors($dealListFields, $filterFields, $requestedFields, $extraFields);
        return $instance;
    }

}