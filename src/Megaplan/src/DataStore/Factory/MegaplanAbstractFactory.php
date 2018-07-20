<?php

namespace rollun\api\megaplan\DataStore\Factory;

use Interop\Container\ContainerInterface;
use rollun\datastore\DataStore\DataStoreException;
use rollun\datastore\DataStore\Factory\DataStoreAbstractFactory;
use rollun\api\megaplan\DataStore\MegaplanDataStore;
use Zend\ServiceManager\Exception\InvalidArgumentException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

class MegaplanAbstractFactory extends DataStoreAbstractFactory
{
    const MEGAPLAN_DATASTORE_SINGLE_ENTITY_KEY = 'singleEntity';
    const MEGAPLAN_DATASTORE_LIST_ENTITY_KEY = 'listEntity';

    protected static $KEY_DATASTORE_CLASS = MegaplanDataStore::class;

    protected static $KEY_IN_CREATE = 0;

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (static::$KEY_IN_CREATE) {
            throw new DataStoreException("Create will be called without pre call canCreate method");
        }
        static::$KEY_IN_CREATE = 1;

        $config = $container->get('config');
        if (!isset($config[self::KEY_DATASTORE][$requestedName])) {
            static::$KEY_IN_CREATE = 0;
            throw new ServiceNotFoundException("Specified service not found");
        }

        $serviceConfig = $config[self::KEY_DATASTORE][$requestedName];
        $requestedClassName = $serviceConfig[self::KEY_CLASS];

        if (!isset($serviceConfig[self::MEGAPLAN_DATASTORE_SINGLE_ENTITY_KEY])) {
            static::$KEY_IN_CREATE = 0;
            throw new InvalidArgumentException("The required parameter \""
                . self::MEGAPLAN_DATASTORE_SINGLE_ENTITY_KEY . "\" is not found in the config.");
        }

        if (!isset($serviceConfig[self::MEGAPLAN_DATASTORE_LIST_ENTITY_KEY])) {
            static::$KEY_IN_CREATE = 0;
            throw new InvalidArgumentException("The required parameter \""
                . self::MEGAPLAN_DATASTORE_LIST_ENTITY_KEY . "\" is not found in the config.");
        }

        $singleEntity = $container->get($serviceConfig[self::MEGAPLAN_DATASTORE_SINGLE_ENTITY_KEY]);
        $listEntity = $container->get($serviceConfig[self::MEGAPLAN_DATASTORE_LIST_ENTITY_KEY]);

        static::$KEY_IN_CREATE = 0;

        return new $requestedClassName($singleEntity, $listEntity);
    }
}