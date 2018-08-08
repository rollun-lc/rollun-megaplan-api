<?php


namespace rollun\api\megaplan\Command;


use rollun\api\megaplan\Exception\InvalidArgumentException;
use rollun\api\megaplan\MegaplanClient;

abstract class AbstractPutMegaplanCommand extends AbstractMegaplanCommand
{
    protected $itemData;

    /**
     * @var array
     */
    protected  $extraFields;

    /**
     * AbstractPutMegaplanCommand constructor.
     * @param MegaplanClient $megaplanClient
     * @param string $uri
     * @param $itemData
     * @param array $extraFields
     */
    public function __construct(MegaplanClient $megaplanClient, string $uri, $itemData, array $extraFields = [])
    {
        parent::__construct($megaplanClient, $uri);
        $this->extraFields = $extraFields;
        $itemData = $this->prepareDataStructure($itemData);
        $this->checkDataStructure($itemData);
        $this->itemData = $itemData;
    }

    /**
     * @return array
     */
    protected function getExtraFields()
    {
        return $this->extraFields;
    }

    /**
     * Prepares request parameters.
     *
     * @return array
     */
    protected function getRequestParams()
    {
        return $this->itemData;
    }

    /**
     * @return string
     */
    public function execute()
    {
        $data = parent::execute();
        return $data[static::KEY_ID];
    }

    /**
     * Change item data struct
     * @param $itemData
     * @return mixed
     */
    protected function prepareDataStructure($itemData)
    {
        $preparedItem = [];
        foreach ($itemData as $key => $value) {
            $fields = preg_grep('/^(?<groupName>[a-zA-Z]+)(?<num>[\d]+)CustomField(?<name>'.$key.')$/', $this->getExtraFields());
            if(empty($fields)) {
                $preparedItem[$key] = $value;
            } elseif(count($fields) == 1) {
                $field = current($fields);
                $preparedItem["Model"][$field] = $value;
            } else {
                throw new \RuntimeException("Get more unique fields.");
            }
        }
        return $preparedItem;
    }

    /**
     * @param $itemData
     * @return void
     */
    protected abstract function checkDataStructure($itemData);
}