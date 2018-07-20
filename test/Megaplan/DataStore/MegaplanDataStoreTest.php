<?php

namespace rollun\test\api\megaplan\DataStore;

use PHPUnit\Framework\TestCase;
use rollun\api\megaplan\Entity\Deal\Deal;
use rollun\api\megaplan\Entity\Deal\Deals;

class MegaplanDataStoreTest extends TestCase
{
    use ContainerMockTrait;

    public function test_read_shouldRunSingleEntityGet()
    {
        $megaplanDataStore = $this->getContainerMock()->get($this->serviceName);
        $this->assertEquals(
            Deal::class . '::' . 'get', $megaplanDataStore->read(1)
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