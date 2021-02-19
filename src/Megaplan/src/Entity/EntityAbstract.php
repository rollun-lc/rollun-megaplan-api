<?php


namespace rollun\api\megaplan\Entity;


abstract class EntityAbstract implements EntityInterface
{
    /**
     * @param false $originKeys
     * @return false|string
     *
     * @todo
     */
    public function toJson(bool $originKeys = false): string
    {
        return json_encode($this->toArray($originKeys));
    }
}