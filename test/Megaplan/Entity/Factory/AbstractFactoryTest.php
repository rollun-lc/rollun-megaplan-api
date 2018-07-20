<?php

namespace rollun\test\api\megaplan\Entity\Factory;

use Mockery;
use PHPUnit\Framework\TestCase;
use rollun\api\megaplan\Entity\Factory\AbstractFactory;
use rollun\test\api\megaplan\Entity\ContainerMockTrait;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

class AbstractFactoryTest extends TestCase
{
    use ContainerMockTrait;

    public function test_factory_invoke_configContainsMegaplanEntitiesSection_shouldDoNothing()
    {
        $factory = new AbstractFactory();
        $factory($this->getContainerMock(), '');
        // If the config doesn't contain necessary section the factory will throw an exception. Then this assert will fail.
        $this->assertTrue(true);
    }

    public function test_factory_configDoesntContainMegaplanEntitiesSection_ShouldThrowException()
    {
        $factory = new AbstractFactory();
        $this->expectException(ServiceNotFoundException::class);
        unset($this->config['megaplan_entities']);
        // Here an exception will be thrown because above we deleted necessary section
        $factory($this->getContainerMock(), '');
    }
}