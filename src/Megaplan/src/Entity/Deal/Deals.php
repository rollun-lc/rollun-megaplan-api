<?php

namespace rollun\api\megaplan\Entity\Deal;

use rollun\api\megaplan\Entity\ExtraFieldsTrait;
use rollun\api\megaplan\Entity\ListEntityAbstract;
use rollun\api\megaplan\Exception\InvalidRequestCountException;

/**
 * Class Deals
 *
 * Allows to receive a list of deals.
 * Note: This entity doesn't allow to receive single deal. To receive one you have to use Deal::class.
 *
 * @package rollun\api\megaplan\Entity
 */
class Deals extends ListEntityAbstract
{
    use ExtraFieldsTrait;

    const URI_ENTITY_GET = '/BumsTradeApiV01/Deal/list.api';

    const ENTITY_DATA_KEY = 'deals';

    /**
     * @var array
     */
    protected $filterFields;

    /**
     * @var array
     */
    protected $requestedFields;

    /**
     * Params which prepared before the first request and changed in each loop iteration
     * @var
     */
    protected $requestParams;

    /**
     * Conditions by which the selection is performed
     *
     * @var array
     */
    protected $conditions = [];

    /**
     * Deals constructor.
     * @param Fields $dealListFields
     * @param array $filterFields
     * @param array $requestedFields
     * @param array $extraFields
     * @throws \rollun\api\megaplan\Exception\InvalidArgumentException
     * @throws \Exception
     */
    public function __construct(Fields $dealListFields,
                                array $filterFields,
                                array $requestedFields = [],
                                array $extraFields = [])
    {
        parent::__construct();
        $this->listFields = $dealListFields;
        $this->filterFields = $filterFields;
        $this->requestedFields = $requestedFields;
        $this->setExtraFields($extraFields);
    }

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    public function get()
    {
        // if there are more than 100 entities we have to collect them in the loop
        $data = [];
        $requestCount = 0;
        $this->reset();
        do {
            $data = array_merge($data, parent::get());

            // check if the limit is exceeded
            $requestCount++;
            if ($requestCount >= static::MAX_REQUEST_COUNT_PRE_HOUR) {
                throw new InvalidRequestCountException("The limit of requests per hour is exceeded");
            }
            // delay
            usleep($this->getRequestInterval());
            // get the next 100 entities
            $this->requestParams['Offset'] += static::MAX_LIMIT;
            // do this while the last part of entities is less than 100 - in this case we reach end of the entities list
        } while(count($data) == $this->requestParams['Offset']);
        return $data;
    }

    /**
     * Returns entities according to specified condition
     * @param $condition
     * @return array|mixed
     * @throws InvalidRequestCountException
     * @throws \Exception
     */
    public function query($condition)
    {
        //Cast all value to array
        $this->conditions = (array)$condition;
        return $this->get();
    }

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     * @throws \Exception
     */
    protected function getRequestParams()
    {
        $this->requestParams = array_merge($this->requestParams, [
            'FilterFields' => $this->buildFilterFields(),
            'RequestedFields' => $this->getRequestedFields(),
            'ExtraFields' => $this->getExtraFields(),
            'Limit' => static::MAX_LIMIT,
        ]);
        return $this->requestParams;
    }

    /**
     * Adds temporary selection's conditions to the permanent filter fields.
     * @return array
     */
    protected function buildFilterFields()
    {
        return array_merge($this->filterFields, $this->conditions);
    }

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
        if (!count($this->extraFields)) {
            $fields = $this->listFields->get();
            foreach ($fields as $field) {
                if (preg_match("/CustomField/", $field['Name'])) {
                    $this->extraFields[] = $field['Name'];
                }
            }
        }
        return $this->extraFields;
    }

    /**
     * Returns basic set of the fields of the deal.
     *
     * @return array
     */
    protected function getRequestedFields()
    {
        return $this->requestedFields;
    }

    /**
     * Resets offset and rebuilds filter fields with the new conditions
     */
    protected function reset()
    {
        $this->requestParams['Offset'] = 0;
        $this->requestParams['FilterFields'] = $this->buildFilterFields();
    }
}