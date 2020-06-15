<?php


namespace rollun\api\megaplan;


use Megaplan\SimpleClient\Client;
use Psr\Log\LoggerInterface;
use rollun\api\megaplan\Exception\ClientException;
use rollun\api\megaplan\Serializer\MegaplanSerializerOptionsInterface;
use rollun\dic\InsideConstruct;
use Zend\Cache\Storage\StorageInterface;
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
     * @var StorageInterface
     */
    private $storage;

    private const STORAGE_KEY = 'megaplan_auth';

    /**
     * MegaplanClient constructor.
     * @param Client $client
     * @param AdapterInterface $serializer
     * @param StorageInterface $storage
     * @throws \ReflectionException
     */
    public function __construct(
        Client $client,
        AdapterInterface $serializer,
        ?StorageInterface $storage,
        LoggerInterface $logger = null
    ) {
        InsideConstruct::init(['logger' => LoggerInterface::class]);
        $this->client = $client;
        $this->serializer = $serializer;
        $this->storage = $storage;
    }

    public function __sleep()
    {
        return [
            'client',
            'serializer',
            'storage'
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
                $this->logger->warning('Megaplan client. Curl error', [
                    'info' => $this->client->getInfo(),
                    'error' => $this->client->getError(),
                    'response' => $response,
                ]);
                throw new \RuntimeException('Megaplan client. Curl error');
            }

            if ($this->client->getInfo('http_code') === 502) {
                $this->logger->warning('Megaplan client. Bad gateway', [
                    'info' => $this->client->getInfo(),
                    'response' => $response,
                ]);
                throw new \RuntimeException('Megaplan client. Bad gateway');
            }

            $this->logger->debug('Megaplan client. Raw response', [
                'info' => $this->client->getInfo(),
                'error' => $this->client->getError(),
                'response' => $response,
            ]);

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

    /**
     * @param $login
     * @param $password
     * @throws \Exception
     */
    public function auth($login, $password)
    {
        $auth = $this->storage ? json_decode($this->storage->getItem(self::STORAGE_KEY)) : null;
        if (!empty($auth) && isset($auth->AccessId) && isset($auth->SecretKey)) {
            $this->client->setAccessId($auth->AccessId);
            $this->client->setSecretKey($auth->SecretKey);
        } else {
            $this->client->auth($login, $password);
            if ($this->storage and $result = $this->client->getResult() and !empty($result->data)) {
                $this->storage->setItem(self::STORAGE_KEY, json_encode($result->data));
            }
        }
    }

}