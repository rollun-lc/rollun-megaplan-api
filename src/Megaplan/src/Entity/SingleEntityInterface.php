<?php

namespace rollun\api\megaplan\Entity;

interface SingleEntityInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param $id
     * @return SingleEntityInterface
     */
    public function setId($id);

    /**
     * Sends a request for creation an entity with specified data.
     *
     * @param $itemData
     * @param bool|false $rewriteIfExist
     * @return array
     */
    public function create($itemData, $rewriteIfExist = false);

    /**
     * Sends a request for update an entity with specified data. Data have to contain entity ID.
     *
     * @param $itemData
     * @param bool|false $createIfAbsent
     * @return array
     */
    public function update($itemData, $createIfAbsent = false);

}