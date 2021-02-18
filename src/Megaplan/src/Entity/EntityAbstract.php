<?php


namespace rollun\api\megaplan\Entity;


abstract class EntityAbstract
{
    /**
     * @param false $onlyChanged
     * @param false $originKeys
     * @return mixed
     *
     * @todo Удалить параметр $onlyChanged
     */
    public abstract function toArray($onlyChanged = false, $originKeys = false);

    /**
     * @param false $originKeys
     * @return false|string
     *
     * @todo
     */
    public function toJson($originKeys = false)
    {
        return json_encode($this->toArray(false, $originKeys));
    }
}