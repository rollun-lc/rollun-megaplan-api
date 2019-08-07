<?php


namespace rollun\api\megaplan;


use Megaplan\SimpleClient\Client;
use Psr\Log\LoggerInterface;
use rollun\api\megaplan\Exception\ClientException;
use rollun\api\megaplan\Serializer\MegaplanSerializerOptionsInterface;
use rollun\dic\InsideConstruct;
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

    /** @var LoggerInterface */
    private $logger;

    /**
     * MegaplanClient constructor.
     * @param Client $client
     * @param AdapterInterface $serializer
     * @throws \ReflectionException
     */
    public function __construct(Client $client, AdapterInterface $serializer, LoggerInterface $logger = null)
    {
        InsideConstruct::init(['logger' => LoggerInterface::class]);
        $this->client = $client;
        $this->serializer = $serializer;
    }

    public function __sleep()
    {
        return [
            'client',
            'serializer',
        ];
    }

    public function __wakeup()
    {
        InsideConstruct::initWakeup(['logger' => LoggerInterface::class]);
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
            if (!empty($entityType)) {
                $this->setEntityType($entityType);
            }
            if (false !== strpos($uri, 'save.api')) {
                $response = $this->client->post($uri, $params);
            } else {
                $response = $this->client->get($uri, $params);
            }
            if ($this->client->getError() !== '' && $this->client->getError() !== null) {
                $this->logger->warning('Megaplan client. Response has error', [
                    'info' => $this->client->getInfo(),
                    'error' => $this->client->getError(),
                    'response' => $response,
                ]);
            } else {
                $this->logger->debug('Megaplan client. Raw response', [
                    'info' => $this->client->getInfo(),
                    'error' => $this->client->getError(),
                    'response' => $response,
                ]);
            }
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