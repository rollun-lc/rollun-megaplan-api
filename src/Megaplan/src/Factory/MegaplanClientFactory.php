<?php

namespace rollun\api\megaplan\Factory;

use Interop\Container\ContainerInterface;
use rollun\api\megaplan\MegaplanClient;
use rollun\api\megaplan\Serializer\MegaplanSerializer;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Megaplan\SimpleClient\Client;

class MegaplanClientFactory implements FactoryInterface
{
    const KEY = 'megaplan';

    const KEY_API_URL = 'api_url';
    const KEY_API_LOGIN = 'login';
    const KEY_API_PASSWORD = 'password';

    const KEY_SERIALIZER = "serializer";

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
            throw new ServiceNotFoundException(
                sprintf("Can't create a service because there is no required data - \"%s\", \"%s\", \"%s\" - in the config.",
                    static::KEY_API_URL, static::KEY_API_LOGIN, static::KEY_API_PASSWORD)
            );
        }

        $instance = new Client($serviceConfig[static::KEY_API_URL]);
        // If both login and password are empty skip an authorization
        if (!(empty($serviceConfig[static::KEY_API_LOGIN]) && empty($serviceConfig[static::KEY_API_PASSWORD]))) {
            $instance->auth($serviceConfig[static::KEY_API_LOGIN], $serviceConfig[static::KEY_API_PASSWORD]);
        }
        $serializer = $container->get($serviceConfig[static::KEY_SERIALIZER] ?? MegaplanSerializer::class);
        return new MegaplanClient($instance, $serializer);
    }
}