<?php

namespace rollun\api\megaplan\DataStore\Factory;

use Interop\Container\ContainerInterface;
use rollun\api\megaplan\DataStore\AbstractMegaplanEntity;
use rollun\datastore\DataStore\DataStoreException;
use rollun\datastore\DataStore\Factory\DataStoreAbstractFactory;
use rollun\api\megaplan\DataStore\ARMegaplanDataStore;
use Laminas\ServiceManager\Exception\InvalidArgumentException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;

class MegaplanAbstractFactory extends DataStoreAbstractFactory
{
    const KEY_MEGAPLAN_COMMAND_BUILDER = 'megaplanCommandBuilder';

    const KEY_PROGRAM_ID = 'programId';

    protected static $KEY_DATASTORE_CLASS = AbstractMegaplanEntity::class;

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

        if (!isset($serviceConfig[self::KEY_MEGAPLAN_COMMAND_BUILDER])) {
            static::$KEY_IN_CREATE = 0;
            throw new InvalidArgumentException("The required parameter \""
                . self::KEY_MEGAPLAN_COMMAND_BUILDER . "\" is not found in the config.");
        }

        if (!isset($serviceConfig[self::KEY_PROGRAM_ID])) {
            if(!preg_match('/([\w\d]+)-(?<program_id>[\d]+)/', $requestedName,$match)) {
                static::$KEY_IN_CREATE = 0;
                throw new InvalidArgumentException("The required parameter \""
                    . self::KEY_PROGRAM_ID . "\" is not found in the config.");
            }
            $programId = $match["program_id"];
        } else {
            $programId = $serviceConfig[self::KEY_PROGRAM_ID];
        }

        $megaplanCommandBuilder = $container->get($serviceConfig[self::KEY_MEGAPLAN_COMMAND_BUILDER]);
        static::$KEY_IN_CREATE = 0;

        return new $requestedClassName($megaplanCommandBuilder, $programId);
    }
}