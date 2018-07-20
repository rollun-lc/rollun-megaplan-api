<?php

namespace rollun\api\megaplan\Serializer;

interface MegaplanSerializerOptionsInterface
{
    /**
     * @return string
     */
    public function getEntity();

    /**
     * @param string $entity
     * @return MegaplanSerializerOptions
     */
    public function setEntity($entity);
}