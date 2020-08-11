<?php


namespace rollun\api\megaplan;


use Megaplan\SimpleClient\Client;
use Psr\Log\LoggerInterface;
use rollun\api\megaplan\Exception\ClientException;
use rollun\api\megaplan\Serializer\MegaplanSerializerOptionsInterface;
use rollun\dic\InsideConstruct;
use rollun\utils\CallAttemptsTrait;
use Zend\Cache\Storage\StorageInterface;
use Zend\Serializer\Adapter\AdapterInterface;

/**
 * Class MegaplanClient
 * @package rollun\api\megaplan
 */
class MegaplanClient
{
    use CallAttemptsTrait;

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
     * @todo Why method name is "get"? Rename
     *
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
                $response = $this->sendPostRequest($uri, $params);
            } else {
                $response = $this->sendGetRequest($uri, $params);
            }

            return $response;
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), [
                'info' => $this->client->getInfo(),
                'error' => $this->client->getError(),
                'uri' => $uri,
                'params' => $params,
                'response' => $response ?? null,
            ]);
            throw new ClientException(
                "By do request get error: {$exception->getMessage()}.",
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @param $uri
     * 
     * @param array|null $params
     * 
     * @return mixed
     * 
     * @throws \Exception
     */
    protected function sendPostRequest($uri, array $params = null)
    {
        $response = $this->client->post($uri, $params);

        if ($this->client->getError() !== '' && $this->client->getError() !== null) {
            throw new \RuntimeException('Megaplan client. Curl error');
        }

        if ($this->client->getInfo('http_code') == 502) {
            throw new \RuntimeException('Megaplan client. Bad gateway');
        }

        // Fetch data from response
        return $this->serializer->unserialize($response);
    }

    /**
     * @param $uri
     * 
     * @param array|null $params
     * 
     * @return array
     * 
     * @throws \Throwable
     */
    protected function sendGetRequest($uri, array $params = null)
    {
        return self::callAttemptsCallable(4, 15000000, function() use ($uri, $params) {
            $response = $this->client->get($uri, $params);

            if ($this->client->getError() !== '' && $this->client->getError() !== null) {
                throw new \RuntimeException('Megaplan client. Curl error');
            }

            if ($this->client->getInfo('http_code') == 502) {
                throw new \RuntimeException('Megaplan client. Bad gateway');
            }

            // Fetch data from response
            return $this->serializer->unserialize($response);
        });
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