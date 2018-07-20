<?php

namespace rollun\api\megaplan\Entity;

use rollun\api\megaplan\Exception\InvalidArgumentException;

abstract class SingleEntityAbstract extends EntityAbstract implements SingleEntityInterface
{
    const ID_OPTION_KEY = 'Id';

    /**
     * Unique identifier
     *
     * @var integer
     */
    protected $id;

    /**
     * The list of fields which can be on top level an array of created/updated entity.
     * No other fields can be here.
     *
     * @var array
     */
    protected $allowedTopLevelDataFields = [];

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Checks if the entity with specified id exits
     *
     * @param $id
     * @return bool
     */
    protected function has($id)
    {
        $prevId = $this->id;
        $this->setId($id);
        // If the deal doesn't exist here an exception will be thrown
        // if it wasn't then just return true
        $this->get();
        $this->setId($prevId);//return prevId.
        return true;
    }

    /**
     * Megaplan protocol requires that top level of a specified data consists of definitely list of fields or their combination.
     *
     * @param $itemData
     * @throws InvalidArgumentException
     */
    protected function checkDataStructure($itemData)
    {
        foreach (array_keys($itemData) as $field) {
            if (!in_array($field, $this->allowedTopLevelDataFields)) {
                throw new InvalidArgumentException("Can't process the deal with field \"{$field}\".");
            }
        }
    }
}