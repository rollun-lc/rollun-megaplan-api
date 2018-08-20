<?php


namespace rollun\api\megaplan\DataStore;

/**
 * Class ContractorEntityFieldsDataSource
 * @package rollun\api\megaplan\DataStore
 */
class ContractorEntityFieldsDataSource implements EntityFieldsDataSourceInterface
{

    private $fields = [
        "Id"
    ];

    private $extraFields = [
        "TypePerson",
        "Type",
        "FirstName",
        "LastName",
        "MiddleName",
        "CompanyName",
        "ParentCompany",
        "Email",
        "Phones",
        "Birthday",
        "Responsibles",
        "ResponsibleContractors",
    ];

    /**
     * @return array Return data of DataSource
     */
    public function getAll()
    {
        return array_merge($this->fields, $this->extraFields);
    }

    /**
     * @param $fieldName
     * @return
     * @throws \rollun\api\megaplan\Exception\InvalidCommandType
     */
    public function warpField($fieldName)
    {
        return $fieldName;
    }

    /**
     * @param $field
     * @return bool
     * @throws \rollun\api\megaplan\Exception\InvalidCommandType
     */
    public function hasField($field)
    {
        return in_array($field, $this->getFields());
    }

    /**
     * @param $field
     * @return bool
     * @throws \rollun\api\megaplan\Exception\InvalidCommandType
     */
    public function hasExtraField($field)
    {
        return in_array($field, $this->getExtraFields());
    }

    /**
     * Return entity extra field list
     * @return array
     * @throws \rollun\api\megaplan\Exception\InvalidCommandType
     */
    public function getExtraFields()
    {
        return $this->extraFields;
    }

    /**
     * Return only entity field list
     * @return array
     * @throws \rollun\api\megaplan\Exception\InvalidCommandType
     */
    public function getFields()
    {
        return $this->fields;
    }
}