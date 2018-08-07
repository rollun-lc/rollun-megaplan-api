<?php


namespace rollun\api\megaplan;


use rollun\api\megaplan\Command\Builder\CommandBuilderPipe;
use rollun\api\megaplan\Command\Builder\RequestByQueryMegaplanCommandBuilder;
use rollun\api\megaplan\Command\Builder\MegaplanCommandBuilderAbstract;
use rollun\api\megaplan\DataStore\Contractors;
use rollun\api\megaplan\DataStore\Deals;
use rollun\api\megaplan\DataStore\Factory\MegaplanAbstractFactory;
use rollun\api\megaplan\Factory\MegaplanClientFactory;
use rollun\api\megaplan\Serializer\MegaplanSerializer;
use rollun\api\megaplan\Serializer\MegaplanSerializerOptions;
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
            AbstractServiceAbstractFactory::KEY => $this->getAbstractServiceAbstractFactory(),
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
            "Deals-13" => [
                MegaplanAbstractFactory::KEY_CLASS => Deals::class,
                MegaplanAbstractFactory::KEY_MEGAPLAN_COMMAND_BUILDER => "MegaplanCommandBuilder",
            ],
            "Contractor-13" => [
                MegaplanAbstractFactory::KEY_CLASS => Contractors::class,
                MegaplanAbstractFactory::KEY_MEGAPLAN_COMMAND_BUILDER => "MegaplanCommandBuilder",
            ],
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
            ],
            'aliases' => [],
        ];
    }
}