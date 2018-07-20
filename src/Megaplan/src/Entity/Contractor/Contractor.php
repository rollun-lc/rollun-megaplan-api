<?php

namespace rollun\api\megaplan\Entity\Contractor;

use InvalidArgumentException;
use rollun\api\megaplan\Entity\Deal\Fields;
use rollun\api\megaplan\Entity\ExtraFieldsTrait;
use rollun\api\megaplan\Entity\SingleEntityAbstract;
use Zend\Serializer\Adapter\AdapterInterface as SerializerAdapterInterface;

class Contractor extends SingleEntityAbstract
{
    use ExtraFieldsTrait;

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    const URI_ENTITY_GET = '/BumsCrmApiV01/Contractor/card.api';

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    const ENTITY_DATA_KEY = 'contractor';

    const PROGRAM_ID_KEY = "ProgramId";

    /**
     * Requested fields (changes the default set of fields)
     *
     * @var array
     */
    protected $requestedFields;

    /**
     * @var
     */
    protected $listFields;

    /**
     * @var string
     */
    private $programId;

    /**
     * Client constructor.
     * @param Fields $listFields
     * @param null $programId
     * @param array $requestedFields
     * @param array $extraFields
     * @throws \Exception
     */
    public function __construct(Fields $listFields, $programId = null, array $requestedFields = [], array $extraFields = [])
    {
        parent::__construct();
        $this->listFields = $listFields;
        $this->programId = $programId;
        $this->requestedFields = $requestedFields;
        $this->setExtraFields($extraFields);
    }

    /**
     * Prepares request parameters.
     *
     * @return array
     */
    protected function getRequestParams()
    {
        if (is_null($this->id)) {
            throw new InvalidArgumentException("The required option \"" . self::ID_OPTION_KEY . "\" is not set.");
        }
        return [
            self::ID_OPTION_KEY => $this->id,
            'RequestedFields' => $this->requestedFields,
        ];
    }

    /**
     * @param $itemData
     * @return array
     * @throws \rollun\api\megaplan\Exception\InvalidArgumentException
     * @throws \Exception
     */
    protected function put($itemData) {
        $itemData = $this->prepareDataStructure($itemData);
        $this->checkDataStructure($itemData);
        if (!isset($itemData[static::PROGRAM_ID_KEY])) {
            $itemData[static::PROGRAM_ID_KEY] = $this->programId;
        }

        $response = $this->megaplanClient->get('/BumsCrmApiV01/Contractor/save.api', $itemData);
        $data = $this->serializer->unserialize($response);

        $dealId = $data[static::ID_OPTION_KEY];
        $this->setId($dealId);
        return $this->get();
    }

    /**
     * @param $itemData
     * @return mixed
     * @throws \Exception
     */
    protected function prepareDataStructure($itemData)
    {
        $preparedItem = [];
        foreach ($itemData as $key => $value) {
            //TODO: add pattern replace /(?<groupName>[a-zA-Z]+)(?<num>[\d]+)CustomField(?<name>[\w]+)/
            if(in_array("$key", $this->getExtraFields())) {
                $preparedItem["Model"][$key] = $value;
            } else {
                $preparedItem[$key] = $value;
            }
        }
        return $preparedItem;
    }

    /**
     * Sends a request for creation an entity with specified data.
     *
     * @param $itemData
     * @param bool|false $rewriteIfExist
     * @return array
     * @throws \rollun\api\megaplan\Exception\InvalidArgumentException
     */
    public function create($itemData, $rewriteIfExist = false)
    {
        if (isset($itemData[static::ID_OPTION_KEY])) {
            if ($this->has($itemData[static::ID_OPTION_KEY]) && !$rewriteIfExist) {
                throw new InvalidArgumentException("Can't create a new deal because the deal with " . static::ID_OPTION_KEY
                    . "=\"{$itemData[static::ID_OPTION_KEY]}\" exists but you didn't allow its rewriting.");
            }
        }
        return $this->put($itemData);
    }

    /**
     * Sends a request for update an entity with specified data. Data have to contain entity ID.
     *
     * @param $itemData
     * @param bool|false $createIfAbsent
     * @return array
     * @throws \rollun\api\megaplan\Exception\InvalidArgumentException
     */
    public function update($itemData, $createIfAbsent = false)
    {
        if (isset($itemData[static::ID_OPTION_KEY])) {
            if (!$this->has($itemData[static::ID_OPTION_KEY]) && !$createIfAbsent) {
                throw new InvalidArgumentException("The deal with " . static::ID_OPTION_KEY
                    . "=\"{$itemData[static::ID_OPTION_KEY]}\" doesn't exist.");
            }
        }
        return $this->put($itemData);
    }
}