<?php

namespace rollun\test\api\megaplanTest\Entity\Factory;

use Mockery;
use PHPUnit\Framework\TestCase;
use rollun\api\megaplan\Entity\Factory\MegaplanClientFactory;
use Megaplan\SimpleClient\Client;
use rollun\test\api\megaplan\Entity\ContainerMockTrait;

class MegaplanClientFactoryTest extends TestCase
{
    use ContainerMockTrait;

    public function test_invoke_correctConfig_shouldReturnMegaplanClientObject()
    {
        $factory = new MegaplanClientFactory();
        $instance = $factory($this->getContainerMock(), '', null);
        $this->assertInstanceOf(
            Client::class, $instance
        );
    }
}