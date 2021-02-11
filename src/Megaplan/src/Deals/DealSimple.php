<?php


namespace rollun\api\megaplan\Deals;


use rollun\utils\Json\Exception;

class DealSimple extends DealAbstract
{
    protected const CONSTANT_FIELDS = [
        'Id',
        'ProgramId',
        'TimeCreated',
        'GUID',
        'Program',
        'Name',
    ];

    protected const MEGAPLAN_CUSTOM_FIELD_PATTERN = '/Category(?<id>[\d]+)CustomField(?<field>[\w]+)/';

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var array
     */
    protected $extraParams = [];

    /**
     * @var array
     */
    protected $changedParams = [];

    /**
     * @var callable|null
     */
    protected $fieldMapper;

    /**
     * @var string
     */
    protected $extraFieldId;


    /**
     * DealObject constructor.
     * @param array $deal
     * @param null $fieldMapper
     */
    public function __construct(array $deal, $fieldMapper = null)
    {
        if (!isset($deal['Id'])) {
            throw new \InvalidArgumentException('Deal without \'Id\'. ');
        }
        foreach ($deal as $field => $value) {
            //Category1000047CustomField
            if (preg_match(self::MEGAPLAN_CUSTOM_FIELD_PATTERN, $field, $match)) {
                $this->extraFieldId = $match['id'];
                $field = $match['field'];
                $this->extraParams[$field] = $value;
            } else {
                $this->params[$field] = $value;
            }
        }
        //id function by default
        $this->fieldMapper = $fieldMapper ?? $this->defaultFieldMapper();
    }

    /**
     * @todo
     */
    protected function defaultFieldMapper()
    {
        return function ($field) {
            return $field;
        };
    }

    /**
     * @param $field
     * @param $value
     */
    public function set($field, $value)
    {
        if (in_array($field, self::CONSTANT_FIELDS, true)) {
            throw new \InvalidArgumentException(sprintf('Field %s is const.', $field));
        }

        if (
            preg_match(self::MEGAPLAN_CUSTOM_FIELD_PATTERN, $field, $match)
            && isset($this->extraParams[$match['field']])
        ) {
            $this->changedParams[$match['field']] = $value;
            $this->extraParams[$field] = $value;
            return;
        }

        $field = call_user_func($this->fieldMapper, $field);
        $this->changedParams[$field] = $value;
        if (isset($this->params[$field])) {
            $this->params[$field] = $value;
        } elseif (isset($this->extraParams[$field])) {
            $this->extraParams[$field] = $value;
        } else {
            throw new \InvalidArgumentException(sprintf('Field %s not found in deal.', $field));
        }
    }

    /**
     * @param $field
     * @return mixed
     */
    public function get($field)
    {
        if (preg_match(self::MEGAPLAN_CUSTOM_FIELD_PATTERN, $field, $match)
            && isset($this->extraFieldId[$match['field']])) {
            return $this->extraParams[$field];
        }

        $field = call_user_func($this->fieldMapper, $field);
        if (isset($this->params[$field])) {
            return $this->params[$field];
        } elseif (isset($this->extraParams[$field])) {
            return $this->extraParams[$field];
        }
        throw new \InvalidArgumentException(sprintf('Field %s not found in deal.', $field));
    }

    public function getExtraFieldName($field) {
        return sprintf('Category%sCustomField%s', $this->extraFieldId, $field);
    }

    /**
     * @param bool $originKeys
     * @param bool $onlyChanged
     * @return array
     *
     * @todo Удалить параметр $onlyChanged
     */
    public function toArray($onlyChanged = false, $originKeys = false): array
    {
        $deal = [
            'Id' => $this->params['Id']
        ];

        $fields = array_keys($this->params);

        foreach ($this->changedParams as $field => $value) {
            if (!in_array($field, $fields, true)) {
                $field = sprintf('Category%sCustomField%s', $this->extraFieldId, $field);
                $deal['Model'][$field] = $value;
            } else {
                $deal[$field] = $value;
            }
        }

        if ($onlyChanged) {
            return $deal;
        }

        foreach ($this->params as $field => $value) {
            if (!isset($deal[$field])) {
                $deal[$field] = $value;
            }
        }

        foreach ($this->extraParams as $field => $value) {

            /*$field = sprintf('Category%sCustomField%s', $this->extraFieldId, $field);
             if (!isset($deal['Model'][$field])) {
                $deal['Model'][$field] = $value;
            }*/
            $field = !$originKeys ? $field : sprintf('Category%sCustomField%s', $this->extraFieldId, $field);
            $deal[$field] = $value;
        }

        return $deal;
    }

    /**
     * @todo
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        try {
            $this->get($offset);
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }

    public function __sleep()
    {
        $fields = [
            'params',
            'extraParams',
            'changedParams',
            'extraFieldId',
        ];

        if (!is_callable($this->fieldMapper)) {
            $fields[] = 'fieldMapper';
        }

        return $fields;
    }

    public function __wakeup()
    {
        if (!$this->fieldMapper) {
            $this->fieldMapper = $this->defaultFieldMapper();
        }
    }
}