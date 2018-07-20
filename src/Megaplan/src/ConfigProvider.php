<?php

namespace rollun\api\megaplan;

use rollun\api\megaplan\DataStore\MegaplanDataStore;
use rollun\api\megaplan\Entity;
use rollun\api\megaplan\Entity\Factory\MegaplanClientFactory;
use rollun\api\megaplan\Serializer\MegaplanSerializer;
use rollun\api\megaplan\Serializer\MegaplanSerializerOptions;
use rollun\api\megaplan\DataStore\Factory\MegaplanAbstractFactory;
use Megaplan\SimpleClient\Client;

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
            'megaplan_entities' => $this->getMegaplanEntities(),
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
                Client::class => MegaplanClientFactory::class,
                Entity\Deal\Deal::class => Entity\Deal\Factory\DealFactory::class,
                Entity\Deal\Deals::class => Entity\Deal\Factory\DealsFactory::class,
                Entity\Deal\Fields::class => Entity\Deal\Factory\FieldsFactory::class,
                Entity\Contractor\Contractor::class => Entity\Contractor\Factory\ContractorFactory::class,
                Entity\Contractor\Contractors::class => Entity\Contractor\Factory\ContractorsFactory::class,
                Entity\Contractor\Fields::class => Entity\Contractor\Factory\FieldsFactory::class,
            ],
            'abstract_factories' => [
                MegaplanAbstractFactory::class,
            ],
            'aliases' => [],
            'shared' => [
                'serializer' => false,
                'options' => false,
            ],
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
            'megaplan_deal_dataStore_service' => [
                'class' => MegaplanDataStore::class,
                'singleEntity' =>  Entity\Deal\Deal::class,
                'listEntity' =>  Entity\Deal\Deals::class,
            ],
            'megaplan_contractor_dataStore_service' => [
                'class' => MegaplanDataStore::class,
                'singleEntity' => Entity\Contractor\Contractor::class,
                'listEntity' => Entity\Contractor\Contractors::class,
            ],
        ];
    }

    /**
     * Returns unchanged parameter of the megaplan_entities section.
     *
     * @return array
     */
    public function getMegaplanEntities()
    {
        return [
            'deals' => [
                'listFields' =>  Entity\Deal\Fields::class,
                'filterField' => [
                    'Program' => 13,
                ],
                'requestedFields' => [],
                'extraFields' => [],
            ],
            'contractors' => [
                'listFields' => Entity\Contractor\Fields::class,
                'filterField' => [
                    'Program' => 13,
                ],
                'requestedFields' => [],
                'extraFields' => [],
            ],
        ];
    }
}