<?php


namespace rollun\api\megaplan;


use Psr\Log\LoggerInterface;
use rollun\api\megaplan\Callback\ResetCounterCallback;
use rollun\api\megaplan\Command\Builder\CommandBuilderPipe;
use rollun\api\megaplan\Command\Builder\RequestByQueryMegaplanCommandBuilder;
use rollun\api\megaplan\Command\Builder\MegaplanCommandBuilderAbstract;
use rollun\api\megaplan\DataStore\Contractors;
use rollun\api\megaplan\DataStore\Deals;
use rollun\api\megaplan\DataStore\Factory\MegaplanAbstractFactory;
use rollun\api\megaplan\Factory\MegaplanCallbackAbstractFactory;
use rollun\api\megaplan\Factory\MegaplanClientFactory;
use rollun\api\megaplan\Factory\MegaplanProcessAbstractFactory;
use rollun\api\megaplan\Factory\MegaplanWebhookMiddlewareFactory;
use rollun\api\megaplan\Middleware\MegaplanWebhookMiddleware;
use rollun\api\megaplan\Serializer\MegaplanSerializer;
use rollun\api\megaplan\Serializer\MegaplanSerializerOptions;
use rollun\callback\Middleware\CallablePluginManagerFactory;
use rollun\callback\Middleware\WebhookMiddlewareFactory;
use rollun\utils\Factory\AbstractServiceAbstractFactory;

class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array
     */
    function __invoke()
    {
        return [
            'dependencies' => $this->getDependencies(),
            'dataStore' => $this->getDataStore(),
            MegaplanClientFactory::KEY => $this->getMegaplan(),
            AbstractServiceAbstractFactory::KEY => $this->getAbstractServiceAbstractFactory(),
            CallablePluginManagerFactory::KEY_INTERRUPTERS => $this->getInterrupters(),
        ];
    }

    protected function getMegaplan()
    {
        return [
            MegaplanClient::class => [
                'api_url' => getenv('MP_URL'),
                'login' => getenv('MP_USER'),
                'password' => getenv('MP_PASS'),
                'timeout' => 60,
                MegaplanClientFactory::KEY_AUTH_CACHE => [
                    'adapter' => [
                        'name' => 'filesystem',
                        'options' => [
                            'ttl' => 3600,
                            'cacheDir' => realpath('./data') . '/cache'
                        ]
                    ],
                    'plugins' => [
                        'exception_handler' => [
                            'throw_exceptions' => false
                        ],
                    ],
                ],
                MegaplanClientFactory::KEY_SAVING_CACHE => [
                    'adapter' => [
                        'name' => 'filesystem',
                        'options' => [
                            'ttl' => 300,
                            'cacheDir' => realpath('./data') . '/cache'
                        ]
                    ],
                    'plugins' => [
                        'exception_handler' => [
                            'throw_exceptions' => false
                        ],
                    ],
                ]
            ],
        ];
    }

    protected function getInterrupters()
    {
        return [
            'abstract_factories' => [
                MegaplanCallbackAbstractFactory::class,
            ]
        ];
    }


    /**
     * Returns the configuration of the service for the DataStore.
     *
     * This section is a constant and it's strongly not recommended to change anything.
     *
     * @return array
     */
    public function getDataStore()
    {
        return [
            /*"Deals-13" => [
                MegaplanAbstractFactory::KEY_CLASS => Deals::class,
                MegaplanAbstractFactory::KEY_MEGAPLAN_COMMAND_BUILDER => "MegaplanCommandBuilder",
            ],
            "Contractor-13" => [
                MegaplanAbstractFactory::KEY_CLASS => Contractors::class,
                MegaplanAbstractFactory::KEY_MEGAPLAN_COMMAND_BUILDER => "MegaplanCommandBuilder",
            ],*/
        ];
    }

    /**
     *
     */
    public function getAbstractServiceAbstractFactory()
    {
        return [
            "MegaplanCommandBuilder" => [
                AbstractServiceAbstractFactory::KEY_CLASS => CommandBuilderPipe::class,
                AbstractServiceAbstractFactory::KEY_DEPENDENCIES => [
                    "commandBuilders" => [
                        AbstractServiceAbstractFactory::KEY_TYPE => AbstractServiceAbstractFactory::TYPE_SERVICES_LIST,
                        AbstractServiceAbstractFactory::KEY_VALUE => [
                            MegaplanCommandBuilderAbstract::class,
                            RequestByQueryMegaplanCommandBuilder::class,
                        ]
                    ]
                ]
            ],
            MegaplanCommandBuilderAbstract::class => [
                "megaplanClient" => MegaplanClient::class,
            ],
            RequestByQueryMegaplanCommandBuilder::class => [
                "megaplanClient" => MegaplanClient::class,
            ],
            ResetCounterCallback::class => [
                'logger' => LoggerInterface::class,
            ]
        ];
    }

    /**
     * Returns the container dependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            'invokables' => [
                MegaplanSerializer::class => MegaplanSerializer::class,
                MegaplanSerializerOptions::class => MegaplanSerializerOptions::class,
            ],
            'factories' => [
                MegaplanClient::class => MegaplanClientFactory::class,
            ],
            'abstract_factories' => [
                MegaplanAbstractFactory::class,
                AbstractServiceAbstractFactory::class,
                MegaplanProcessAbstractFactory::class,
                MegaplanCallbackAbstractFactory::class,
            ],
            'aliases' => [],
        ];
    }
}