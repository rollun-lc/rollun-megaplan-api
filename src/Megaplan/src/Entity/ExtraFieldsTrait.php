<?php


namespace rollun\api\megaplan\Entity;


trait ExtraFieldsTrait
{
    /**
     * @var ListEntityAbstract
     */
    protected $listFields;

    /**
     * @var array
     */
    protected $extraFields = [];

    /**
     * Gets extra fields for the entity.
     *
     * Extra fields are custom fields. They contain 'CustomField' chunk in their names.
     * This method gets all the deal fields and then fetch the custom fields only.
     *
     * @return array
     * @throws \Exception
     */
    protected function getExtraFields()
    {
        if (empty($this->extraFields)) {
            $this->extraFields = $this->receivedExtraFields();
        }
        return $this->extraFields;
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function receivedExtraFields() {
        $extraFields = [];
        $fields = $this->listFields->get();
        foreach ($fields as $field) {
            if (preg_match("/CustomField/", $field['Name'])) {
                $extraFields[] = $field['Name'];
            }
        }
        return $extraFields;
    }

    /**
     * @param array $extraFields
     * @throws \Exception
     */
    protected function setExtraFields($extraFields = []) {
        $this->extraFields = empty($extraFields) ? $this->receivedExtraFields() : $extraFields;
    }
}