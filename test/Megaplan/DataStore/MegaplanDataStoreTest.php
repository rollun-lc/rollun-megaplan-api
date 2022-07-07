<?php

namespace rollun\test\api\megaplan\DataStore;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use rollun\api\megaplan\DataStore\Deals;

class MegaplanDataStoreTest extends TestCase
{
    use ContainerMockTrait;

    public function test_read_shouldRunSingleEntityGet()
    {
        $megaplanDataStore = $this->getContainerMock()->get($this->serviceName);
        $this->assertEquals(
            Deals::class . '::' . 'get', $megaplanDataStore->read(1)
        );
    }

    public function test_getAll_shouldRunListEntityGet()
    {
        $megaplanDataStore = $this->getContainerMock()->get($this->serviceName);
        $this->assertEquals(
            Deals::class . '::' . 'get', $megaplanDataStore->getAll()
        );
    }
}