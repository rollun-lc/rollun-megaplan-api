<?php


namespace rollun\api\megaplan\DataStore;

interface EntityFieldsDataSourceInterface
{

    /**
     * @param $fieldName
     * @return
     * @throws \rollun\api\megaplan\Exception\InvalidCommandType
     */
    public function warpField($fieldName);

    /**
     * @param $field
     * @return bool
     * @throws \rollun\api\megaplan\Exception\InvalidCommandType
     */
    public function hasField($field);

    /**
     * @param $field
     * @return bool
     * @throws \rollun\api\megaplan\Exception\InvalidCommandType
     */
    public function hasExtraField($field);

    /**
     * Return entity extra field list
     * @return array
     * @throws \rollun\api\megaplan\Exception\InvalidCommandType
     */
    public function getExtraFields();

    /**
     * Return only entity field list
     * @return array
     * @throws \rollun\api\megaplan\Exception\InvalidCommandType
     */
    public function getFields();
}
