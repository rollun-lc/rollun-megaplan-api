<?php


namespace rollun\api\megaplan;


use Megaplan\SimpleClient\Client;
use rollun\api\megaplan\Exception\ClientException;
use rollun\api\megaplan\Serializer\MegaplanSerializerOptionsInterface;
use Zend\Serializer\Adapter\AdapterInterface;

/**
 * Class MegaplanClient
 * @package rollun\api\megaplan
 */
class MegaplanClient
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var AdapterInterface
     */
    private $serializer;

    /**
     * MegaplanClient constructor.
     * @param Client $client
     * @param AdapterInterface $serializer
     */
    public function __construct(Client $client, AdapterInterface $serializer)
    {
        $this->client = $client;
        $this->serializer = $serializer;
    }

    /**
     * @param $uri
     * @param array|null $params
     * @param string $entityType
     * @return mixed
     */
    public function get($uri, array $params = null, $entityType = "")
    {
        try {
            if(!empty($entityType)) {
                $this->setEntityType($entityType);
            }
            $response = $this->client->get($uri, $params);
            // Fetch data from response
            $data = $this->serializer->unserialize($response);
            return $data;
        } catch (\Exception $exception) {
            throw new ClientException("By do request get error: {$exception->getMessage()}.", $exception->getCode(), $exception);
        }
    }

    /**
     * @param $entityType
     */
    private function setEntityType($entityType)
    {
        if (
            method_exists($this->serializer, "getOptions") &&
            $this->serializer->getOptions() instanceof MegaplanSerializerOptionsInterface) {
            $this->serializer->getOptions()->setEntity($entityType);
        }
    }

}