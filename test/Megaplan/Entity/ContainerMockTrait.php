<?php

namespace rollun\test\api\megaplan\Entity;

use rollun\api\megaplan\Serializer\MegaplanSerializerOptions;
use rollun\datastore\DataStore\DataStoreAbstract;
use Zend\Serializer\Serializer;
use rollun\api\megaplan\Serializer\MegaplanSerializer as MegaplanSerializer;
use Megaplan\SimpleClient\Client;
use Interop\Container\ContainerInterface;
use Mockery;
use rollun\api\megaplan\Entity\Deal\Fields;

trait ContainerMockTrait
{
    protected $config = [
        'megaplan' => [
            'api_url' => 'amazon.megaplan.ua',
            'login' => '',
            'password' => '',
        ],
        'megaplan_entities' => [
            'deals' => [
                'dealListFields' => 'dealListFields',
                'filterField' => [
                    \rollun\api\megaplan\Entity\Deal\Factory\DealsFactory::FILTER_FIELD_PROGRAM_KEY => 6,
                ],
                'requestedFields' => [],
                'extraFields' => [],
            ],
        ],
        'dataStore' => [
            'megaplan_deal_dataStore_service' => [
                'singleEntity' => 'dealEntity',
                'listEntity' => 'dealsEntity',
                'class' => \rollun\api\megaplan\DataStore\MegaplanDataStore::class,
            ],
        ],
    ];

    protected function setUp()
    {
        // I have to do this because megaplan entities use InsideConstructor which builds $container from real config.
        global $container;
        $container = $this->getContainerMock();
    }

    protected function getContainerMock()
    {
        $containerMock = Mockery::mock(ContainerInterface::class);
        $containerMock->shouldReceive('get')
            ->andReturnUsing(function ($serviceName) {
                switch ($serviceName) {
                    case 'config':
                        $instance = $this->getConfig();
                        break;
                    case 'some_dataStore':
                        $instance = $this->getDataStoreMock();
                        $instance->shouldReceive('create')
                            ->andReturn(true);
                        break;
                    case 'serializer':
                        $instance = $this->getSerializer();
                        break;
                    case 'serializerOptions':
                        $instance = $this->getSerializerOptions();
                        break;
                    case 'megaplan':
                        $instance = $this->getMegaplanClientMock();
                        break;
                    case 'dealListFields':
                        $instance = $this->getDealListFields();
                        break;
                    default:
                        throw new \Exception("Can't create service because I'm mock!!");
                        break;
                }
                return $instance;
            });
        return $containerMock;
    }

    protected function getConfig()
    {
        return $this->config;
    }

    protected function getDataStoreMock()
    {
        return Mockery::mock(DataStoreAbstract::class);
    }

    protected function getSerializer()
    {
        return Serializer::factory(MegaplanSerializer::class);
    }

    protected function getSerializerOptions()
    {
        return new MegaplanSerializerOptions();
    }

    protected function getMegaplanClientMock()
    {
        return Mockery::mock(Client::class);
    }

    protected function getDealListFields()
    {
        return Mockery::mock(Fields::class);
    }
}