<?php

namespace rollun\test\api\megaplan\DataStore\Factory;

use PHPUnit\Framework\TestCase;
use rollun\api\megaplan\DataStore\Factory\MegaplanAbstractFactory;
use rollun\datastore\DataStore\DataStoreAbstract;
use rollun\test\api\megaplan\DataStore\ContainerMockTrait;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Exception\InvalidArgumentException;

class MegaplanDataStoreAbstractFactoryTest extends TestCase
{
    use ContainerMockTrait;

    public function test_invoke_correctConfig_shouldReturnMegaplanDataStoreObject()
    {
        $factory = new MegaplanAbstractFactory();
        $instance = $factory($this->getContainerMock(), $this->serviceName);
        $this->assertInstanceOf(
            DataStoreAbstract::class, $instance
        );
        return $factory;
    }

    /**
     * @depends test_invoke_correctConfig_shouldReturnMegaplanDataStoreObject
     * @param $factory
     */
    public function test_invoke_serviceSectionAbsent_shouldThrowException($factory)
    {
        unset($this->config['dataStore'][$this->serviceName]);
        $this->expectException(ServiceNotFoundException::class);
        $this->expectExceptionMessage("Specified service not found");
        $factory($this->getContainerMock(), $this->serviceName);
    }

    /**
     * @depends test_invoke_correctConfig_shouldReturnMegaplanDataStoreObject
     * @param $factory
     */
    public function test_invoke_singleEntityKeyAbsent_shouldThrowException($factory)
    {
        unset($this->config['dataStore'][$this->serviceName]['singleEntity']);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The required parameter \"singleEntity\" is not found in the config.");
        $factory($this->getContainerMock(), $this->serviceName);
    }

    /**
     * @depends test_invoke_correctConfig_shouldReturnMegaplanDataStoreObject
     * @param $factory
     */
    public function test_invoke_listEntityKeyAbsent_shouldThrowException($factory)
    {
        unset($this->config['dataStore'][$this->serviceName]['listEntity']);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The required parameter \"listEntity\" is not found in the config.");
        $factory($this->getContainerMock(), $this->serviceName);
    }
}