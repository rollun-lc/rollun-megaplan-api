<?php


namespace rollun\api\megaplan\DataStore;


use rollun\api\megaplan\Command\AbstractMegaplanCommand;
use rollun\api\megaplan\Command\Builder\CommandBuilderInterface;
use rollun\api\megaplan\Command\RequestEntitiesMegaplanCommand;
use rollun\datastore\DataStore\Interfaces\DataSourceInterface;

class MegaplanEntityFieldsDataSource implements DataSourceInterface
{

    /**
     * @var string
     */
    private $listFieldsUri;

    /**
     * @var string
     */
    private $programId;

    /**
     * @var CommandBuilderInterface
     */
    private $commandBuilder;

    /**
     * @var array
     */
    private $fields = [];

    /**
     * ExtraFieldsDataSource constructor.
     * @param CommandBuilderInterface $commandBuilder
     * @param $listFieldsUri
     * @param $programId
     */
    public function __construct(CommandBuilderInterface $commandBuilder, $listFieldsUri, $programId)
    {
        $this->commandBuilder = $commandBuilder;
        $this->listFieldsUri = $listFieldsUri;
        $this->programId = $programId;
    }

    /**
     * @return mixed
     * @throws \rollun\api\megaplan\Exception\InvalidCommandType
     */
    private function requestFields()
    {
        $command = $this->commandBuilder->build(
            RequestEntitiesMegaplanCommand::class,
            $this->listFieldsUri,
            [
                RequestEntitiesMegaplanCommand::KEY_PROGRAM_ID => $this->programId,
            ]
        );
        return $command->execute();
    }

    /**
     * @param $fieldName
     * @return
     * @throws \rollun\api\megaplan\Exception\InvalidCommandType
     */
    public function warpField($fieldName)
    {
        $fields = preg_grep('/(?<groupName>[a-zA-Z]+)(?<num>[\d]+)CustomField(?<name>' . $fieldName . ')/', $this->getExtraFields());
        if (empty($fields)) {
            return $fieldName;
        } elseif (count($fields) == 1) {
            $field = current($fields);
            return $field;
        } else {
            throw new \RuntimeException("Get more unique fields.");
        }
    }

    /**
     * @return array Return data of DataSource
     * @throws \rollun\api\megaplan\Exception\InvalidCommandType
     */
    public function getAll()
    {
        if (empty($this->fields)) {
            $this->fields = array_map(function ($field) {
                return $field["Name"];
            }, $this->requestFields());
            //nor return by megaplan but need
            $this->fields[] = AbstractMegaplanCommand::KEY_ID;
            $this->fields[] = AbstractMegaplanCommand::KEY_GUID;
        }
        return $this->fields;
    }

    /**
     * @param $field
     * @return bool
     * @throws \rollun\api\megaplan\Exception\InvalidCommandType
     */
    public function hasField($field)
    {
        return isset($this->getFields()[$field]);
    }

    /**
     * @param $field
     * @return bool
     * @throws \rollun\api\megaplan\Exception\InvalidCommandType
     */
    public function hasExtraField($field)
    {
        return isset($this->getExtraFields()[$field]);
    }

    /**
     * @param $fieldName
     * @return false|int
     */
    private function isExtraFiled($fieldName)
    {
        return preg_match("/CustomField/", $fieldName);
    }

    /**
     * @param $fieldName
     * @return bool
     */
    private function isFiled($fieldName)
    {
        return !$this->isExtraFiled($fieldName);
    }

    /**
     * Return entity extra field list
     * @return array
     * @throws \rollun\api\megaplan\Exception\InvalidCommandType
     */
    public function getExtraFields()
    {
        $fields = $this->getAll();
        return array_filter($fields, [$this, "isExtraFiled"]);
    }

    /**
     * Return only entity field list
     * @return array
     * @throws \rollun\api\megaplan\Exception\InvalidCommandType
     */
    public function getFields()
    {
        $fields = $this->getAll();
        return array_filter($fields, [$this, "isFiled"]);
    }
}