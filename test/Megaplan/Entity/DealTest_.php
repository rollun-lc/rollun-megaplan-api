<?php

namespace rollun\test\api\megaplan\Entity;

use Megaplan\SimpleClient\Client;
use Mockery;
use PHPUnit\Framework\TestCase;
use rollun\api\megaplan\Entity\Factory\AbstractFactory;
use rollun\installer\Command;

class DealTest_ extends TestCase
{
    use ContainerMockTrait;

    protected function getMegaplanClientMock()
    {
        $instance = Mockery::mock(Client::class);
        $instance->shouldReceive('get')
            ->once()
            ->andReturnUsing(function() {
                $jsonString = file_get_contents(Command::getDataDir() . 'deals.json');
                return $jsonString;
            });
        $instance->shouldReceive('get')
            ->andReturn('{"status":{"code":"ok","message":null},"data":{"deals":[]}}');
        return $instance;
    }


    public function test_get_shouldReturnArray()
    {
        $factory = new AbstractFactory();
        $instance = $factory($this->getContainerMock(), 'deal_service');
        $result = $instance->get();
        $this->assertCount(
            100, $result
        );
        // Check random item if it has necessary structure
        $index = rand(0, 99);
        $row = $result[$index];
        $deal = json_decode($row['deals'], true);
        $this->assertEquals(
            $row['id'], $deal['Id']
        );
    }
}