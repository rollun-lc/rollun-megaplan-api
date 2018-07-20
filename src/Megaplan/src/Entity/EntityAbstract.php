<?php

namespace rollun\api\megaplan\Entity;

use Megaplan\SimpleClient\Client;
use rollun\api\megaplan\Serializer\MegaplanSerializerOptionsInterface;
use rollun\dic\InsideConstruct;
use Zend\Serializer\Adapter\AdapterInterface as SerializerAdapterInterface;

abstract class EntityAbstract
{
    /**
     * An URI by which you can get an entity
     */
    const URI_ENTITY_GET = '';

    /**
     * A key of a response where real entity data will be returned.
     * The response has the following structure:
     * $response = array(
     *     status => array(
     *         code => '',
     *         message => '',
     *     ),
     *     data => array(
     *         self::ENTITY_DATA_KEY => array(
     *             // ...
     *         ),
     *     ),
     * )
     */
    const ENTITY_DATA_KEY = '';

    /** @var Client */
    protected $megaplanClient;

    /** @var SerializerAdapterInterface */
    protected $serializer;

    /**
     * EntityAbstract constructor.
     * @param Client $megaplanClient
     * @param SerializerAdapterInterface $serializer
     */
    public function __construct(Client $megaplanClient = null, SerializerAdapterInterface $serializer = null)
    {
        InsideConstruct::setConstructParams();
        if (method_exists($this->serializer, "getOptions") && $this->serializer->getOptions() instanceof MegaplanSerializerOptionsInterface) {
            $this->serializer->getOptions()->setEntity(static::ENTITY_DATA_KEY);
        }
    }

    /**
     * Gets information from Megaplan.
     *
     * This is the main method for receive data from Megaplan.
     *
     * @return array
     * @throws \Exception
     */
    public function get()
    {
        $requestParams = $this->getRequestParams();
        $response = $this->megaplanClient->get(static::URI_ENTITY_GET, $requestParams);
        // Fetch data from response
        $data = $this->serializer->unserialize($response);
        return $data;
    }

    /**
     * Prepares request parameters.
     *
     * @return array
     */
    abstract protected function getRequestParams();
}