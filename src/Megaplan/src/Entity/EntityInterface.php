<?php


namespace rollun\api\megaplan\Entity;


interface EntityInterface
{
    /**
     * Получает
     * @param false $onlyChanged
     * @param false $originKeys
     * @return array
     */
    public function toArray(bool $originKeys = false): array;

    /**
     * Возвращает массив полей, которые были изменены
     *
     * @param bool $originKeys
     * @return array
     */
    public function getChanges(bool $originKeys = false): array;

    /**
     * @param false $originKeys
     * @return string
     *
     * @todo
     */
    public function toJson(bool $originKeys = false): string;
}