<?php

namespace rollun\api\megaplan\Serializer;

use Laminas\Serializer\Adapter\JsonOptions;
use Laminas\Serializer\Exception\InvalidArgumentException;

class MegaplanSerializerOptions extends JsonOptions implements MegaplanSerializerOptionsInterface
{
    protected $entity;

    /**
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param string $entity
     * @return MegaplanSerializerOptions
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }
}