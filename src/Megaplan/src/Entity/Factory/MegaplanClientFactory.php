<?php

namespace rollun\api\megaplan\Entity\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Megaplan\SimpleClient\Client;

class MegaplanClientFactory implements FactoryInterface
{
    const KEY = 'megaplan';
    const API_URL_KEY = 'api_url';
    const API_LOGIN_KEY = 'login';
    const API_PASSWORD_KEY = 'password';

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
        // Required megaplan connection params are expected
        if (!(isset($serviceConfig[static::API_URL_KEY]) &&
            isset($serviceConfig[static::API_LOGIN_KEY]) &&
            isset($serviceConfig[static::API_PASSWORD_KEY])
        )) {
            throw new ServiceNotFoundException(
                sprintf("Can't create a service because there is no required data - \"%s\", \"%s\", \"%s\" - in the config.",
                    static::API_URL_KEY, static::API_LOGIN_KEY, static::API_PASSWORD_KEY)
            );
        }

        $instance = new Client($serviceConfig[static::API_URL_KEY]);
        // If both login and password are empty skip an authorization
        if (!(empty($serviceConfig[static::API_LOGIN_KEY]) && empty($serviceConfig[static::API_PASSWORD_KEY]))) {
            $instance->auth($serviceConfig[static::API_LOGIN_KEY], $serviceConfig[static::API_PASSWORD_KEY]);
        }
        return $instance;
    }
}