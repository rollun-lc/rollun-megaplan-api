<?php


namespace rollun\api\megaplan\DataStore;

/**
 * Class ContractorEntityFieldsDataSource
 * @package rollun\api\megaplan\DataStore
 */
class OfferEntityFieldsDataSource implements EntityFieldsDataSourceInterface
{


    /**
     * @inheritDoc
     */
    public function warpField($fieldName)
    {
        return $fieldName;
    }

    /**
     * @inheritDoc
     */
    public function hasField($field)
    {
        return in_array($field, $this->getFields(), true);
    }

    /**
     * @inheritDoc
     */
    public function hasExtraField($field)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getExtraFields()
    {
        return [''];
    }

    /**
     * @inheritDoc
     */
    public function getFields()
    {
        return [
            'Id', 'Name', 'Price', 'Unit', 'Article',
        ];
    }
}