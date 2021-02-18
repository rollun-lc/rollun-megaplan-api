<?php


namespace rollun\api\megaplan\Entity;


interface EntityInterface
{
    /**
     * @param false $onlyChanged
     * @param false $originKeys
     * @return array
     *
     * @todo Удалить параметр $onlyChanged
     */
    public function toArray(): array;

    /**
     * @param false $originKeys
     * @return string
     *
     * @todo
     */
    public function toJson(): string;
}