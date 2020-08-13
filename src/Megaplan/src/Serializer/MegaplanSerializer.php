<?php

namespace rollun\api\megaplan\Serializer;

use rollun\api\megaplan\Exception\InvalidResponseException;
use rollun\dic\InsideConstruct;
use RuntimeException;
use Zend\Serializer\Adapter\Json;

/**
 * Class MegaplanSerializer
 * @package rollun\api\megaplan\Serializer
 * @property MegaplanSerializerOptionsInterface $options
 */
class MegaplanSerializer extends Json
{
    /**
     * MegaplanSerializer constructor.
     * @param MegaplanSerializerOptionsInterface $options
     */
    public function __construct(MegaplanSerializerOptionsInterface $options = null)
    {
        parent::__construct($this->options);
    }

    /**
     * Deserialize an incoming JSON-string to array.
     *
     * The incoming string is JSON-string in a Megaplan response format which has the following view:
     * Array
     * (
     *     [status] => Array
     *         (
     *             [code] => ok
     *             [message] =>
     *         )
     *
     *     [data] => Array
     *         (
     *             [deals] => Array
     *                 (
     *                     [0] => Array
     *                         (
     *                             // Deal data
     *                         )
     *                     [1] => Array
     *                         (
     *                             // Deal data
     *                         )
     *                     // ...
     *                     [N] => Array
     *                         (
     *                             // Deal data
     *                         )
     *                 )
     *         )
     * )
     * Surely the data in this format is not writable to DataStore.
     * So this serializer based on Zend\Serializer\Adapter\Json just extract the raw data from scope.
     * And then create outcoming array the following view:
     * Array
     * (
     *     ['id'] => $id,
     *     ['deal'] => json_encode(['deal']),
     * )
     * That's all. No other changes are made to the data.
     *
     * @param string $serialized
     * @return array
     * @throws InvalidResponseException
     */
    public function unserialize($serialized)
    {
        // Data may come already in stdClass view
        if (!(is_string($serialized) && (is_object(json_decode($serialized)) || is_array(json_decode($serialized))))) {
            // So encode them again
            $serialized = parent::serialize($serialized);
        }
        // Now decode data with $assoc = true
        $unserializedData = parent::unserialize($serialized);

        /**
         * API returns not number of error. Instead "error" or "ok"
         */
        if ('error' === $unserializedData['status']['code']) {
            throw new InvalidResponseException('Response error message: ' . $unserializedData['status']["message"]);
        }

        $rawUnserializedData = current($unserializedData["data"]);
        return $rawUnserializedData;
    }
}