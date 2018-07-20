<?php

namespace rollun\test\api\Api\Serializer;

use PHPUnit\Framework\TestCase;
use rollun\api\megaplan\Serializer\MegaplanSerializer;
use rollun\api\megaplan\Serializer\MegaplanSerializerOptions;
use rollun\installer\Command;

class MegaplanSerializerTest extends TestCase
{
    protected $response;

    /**
     * @var MegaplanSerializer
     */
    protected $serializer;

    protected function setUp()
    {
        $this->response = file_get_contents(Command::getDataDir() . 'deals.json');
        $serializerOptions = new MegaplanSerializerOptions();
        $serializerOptions->setEntity('deals');
        $this->serializer = new MegaplanSerializer($serializerOptions);
    }

    public function test_unserialize_receiveJsonString_shouldReturnArray()
    {
        $data = $this->serializer->unserialize($this->response);
        $this->assertTrue(
            is_array($data)
        );
    }

    public function test_unserialize_receiveStdClass_shouldReturnArray()
    {
        $this->response = json_decode($this->response);
        $this->assertInstanceOf(
            'stdClass', $this->response
        );

        $data = $this->serializer->unserialize($this->response);
        $this->assertTrue(
            is_array($data)
        );
    }

    public function test_unserialize_receiveArray_shouldReturnArray()
    {
        $this->response = json_decode($this->response, true);
        $this->assertTrue(
            is_array($this->response)
        );

        $data = $this->serializer->unserialize($this->response);
        $this->assertTrue(
            is_array($data)
        );
    }
}