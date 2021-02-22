<?php


namespace rollun\api\megaplan\Factory;


use Interop\Container\ContainerInterface;
use rollun\api\megaplan\Callback\MegaplanCallback;
use rollun\api\megaplan\MegaplanClient;
use rollun\callback\Callback\Factory\CallbackAbstractFactoryAbstract;

class MegaplanCallbackAbstractFactory extends CallbackAbstractFactoryAbstract
{
    const KEY = self::class;

    const KEY_CLASS = 'class';

    const DEFAULT_CLASS = MegaplanCallback::class;

    const KEY_CALLBACK = 'callback';

    const KEY_ENTITY_FACTORY = 'entityFactory';

    const KEY_ENTITY_CLASS = 'entityClass';

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        $serviceConfig = $config[static::KEY][$requestedName];

        $callback = $container->get($serviceConfig[self::KEY_CALLBACK]);

        $className = $serviceConfig[static::KEY_CLASS];

        // TODO
        if (isset($serviceConfig[self::KEY_ENTITY_FACTORY])) {
            if (!class_exists($serviceConfig[self::KEY_ENTITY_FACTORY])) {
                throw new \Exception('Can not create deal factory ' . $serviceConfig[self::KEY_ENTITY_FACTORY]);
            }

            // TODO Get from container
            //$client = $container->get(MegaplanClient::class);
            if ($container->has($serviceConfig[self::KEY_ENTITY_FACTORY])) {
                $factory = $container->get($serviceConfig[self::KEY_ENTITY_FACTORY]);
            } else {
                $entityClass = $serviceConfig[self::KEY_ENTITY_CLASS] ?? null;
                $factory = new $serviceConfig[self::KEY_ENTITY_FACTORY]($entityClass);
            }
            //$value = $factoryConfig($container, $requestedName, $options);
        } elseif (isset($serviceConfig[self::KEY_ENTITY_CLASS])) {
            $factory = new EntityFactorySimple($serviceConfig[self::KEY_ENTITY_CLASS]);
        }

        $instance = new $className($callback, $factory);

        return $instance;
    }
}