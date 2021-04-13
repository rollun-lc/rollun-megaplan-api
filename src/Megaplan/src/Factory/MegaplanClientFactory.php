<?php

namespace rollun\api\megaplan\Factory;

use Interop\Container\ContainerInterface;
use rollun\api\megaplan\MegaplanClient;
use rollun\api\megaplan\Serializer\MegaplanSerializer;
use Zend\Cache\StorageFactory;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Megaplan\SimpleClient\Client;

class MegaplanClientFactory implements FactoryInterface
{
    const KEY = 'megaplan';

    const KEY_API_URL = 'api_url';
    const KEY_API_LOGIN = 'login';
    const KEY_API_PASSWORD = 'password';
    const KEY_API_TIMEOUT = 'timeout';

    const KEY_SERIALIZER = "serializer";
    const KEY_AUTH_CACHE = 'auth_cache';
    const KEY_SAVING_CACHE = 'saving_cache';

    const KEY_DEBUG_MODE = 'debug_mode';

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');

        if (!isset($config[static::KEY])) {
            throw new ServiceNotFoundException(
                sprintf("Can't create a service because there is no section \"%s\" in the config.", static::KEY)
            );
        }

        $serviceConfig = $config[static::KEY];
        if(isset($serviceConfig[$requestedName]) && is_array($serviceConfig[$requestedName])) {
            $serviceConfig = $serviceConfig[$requestedName];
        }

        // Required megaplan connection params are expected
        if (!(isset($serviceConfig[static::KEY_API_URL]) &&
            isset($serviceConfig[static::KEY_API_LOGIN]) &&
            isset($serviceConfig[static::KEY_API_PASSWORD])
        )) {
            $message = sprintf(
                "Can't create a service because there is no required data - \"%s\", \"%s\", \"%s\" - in the config.",
                static::KEY_API_URL,
                static::KEY_API_LOGIN,
                static::KEY_API_PASSWORD
            );
            throw new ServiceNotFoundException($message);
        }

        $timeout = $serviceConfig[static::KEY_API_TIMEOUT] ?? 10;
        $client = new Client($serviceConfig[static::KEY_API_URL], $timeout);

        $serializer = $container->get($serviceConfig[static::KEY_SERIALIZER] ?? MegaplanSerializer::class);

        $authCache = $savingCache = null;

        $authCacheConfig = $serviceConfig[static::KEY_AUTH_CACHE] ?? null;
        if (is_array($authCacheConfig) || $authCacheConfig instanceof \Traversable) {
            $authCache = StorageFactory::factory($authCacheConfig);
        }

        $savingCacheConfig = $serviceConfig[static::KEY_SAVING_CACHE] ?? null;
        if (is_array($savingCacheConfig) || $savingCacheConfig instanceof \Traversable) {
            $savingCache = StorageFactory::factory($savingCacheConfig);
        }

        $debugMode = $serviceConfig[static::KEY_DEBUG_MODE] ?? false;

        $instance = new MegaplanClient($client, $serializer, $authCache, $savingCache, $debugMode);

        // If both login and password are empty skip an authorization
        if (!(empty($serviceConfig[static::KEY_API_LOGIN]) && empty($serviceConfig[static::KEY_API_PASSWORD]))) {
            $instance->auth($serviceConfig[static::KEY_API_LOGIN], $serviceConfig[static::KEY_API_PASSWORD]);
        }

        return $instance;
    }
}