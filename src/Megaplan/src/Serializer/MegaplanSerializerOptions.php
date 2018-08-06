<?php

namespace rollun\api\megaplan\Serializer;

use Zend\Serializer\Adapter\JsonOptions;
use Zend\Serializer\Exception\InvalidArgumentException;

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