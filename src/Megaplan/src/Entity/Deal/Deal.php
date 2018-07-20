<?php

namespace rollun\api\megaplan\Entity\Deal;

use rollun\api\megaplan\Entity\ExtraFieldsTrait;
use rollun\api\megaplan\Entity\SingleEntityAbstract;
use rollun\api\megaplan\Exception\InvalidArgumentException;

class Deal extends SingleEntityAbstract
{
    use ExtraFieldsTrait;
    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    const URI_ENTITY_GET = '/BumsTradeApiV01/Deal/card.api';

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    const ENTITY_DATA_KEY = 'deal';

    const PROGRAM_ID_KEY = 'ProgramId';

    /**
     * The list of fields which can be on top level an array of created/updated entity.
     * No other fields can be here.
     *
     * @var array
     */
    protected $allowedTopLevelDataFields = [
        self::ID_OPTION_KEY,
        'ProgramId',
        'StatusId',
        'StrictLogic',
        'Model',
        'Positions',
    ];

    protected $programId;

    /**
     * Requested fields (changes the default set of fields)
     *
     * @var array
     */
    protected $requestedFields;


    /**
     * Deal constructor.
     * @param Fields $dealListFields
     * @param \Megaplan\SimpleClient\Client $programId
     * @param array $requestedFields
     * @param array $extraFields
     * @throws \Exception
     */
    public function __construct(Fields $dealListFields, $programId = null, array $requestedFields = [], array $extraFields = [])
    {
        parent::__construct();
        $this->listFields = $dealListFields;
        $this->programId = $programId;
        $this->requestedFields = $requestedFields;
        $this->setExtraFields($extraFields);
    }

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     * @throws InvalidArgumentException
     * @throws \Exception
     */
    protected function getRequestParams()
    {
        if (is_null($this->id)) {
            throw new InvalidArgumentException("The required option \"" . self::ID_OPTION_KEY . "\" is not set.");
        }
        $requestParams = [
            self::ID_OPTION_KEY => $this->id,
            'RequestedFields' => $this->requestedFields,
            'ExtraFields' => $this->getExtraFields(),
        ];
        return $requestParams;
    }

    protected function checkDataStructure($itemData)
    {
        if (!(isset($itemData[static::PROGRAM_ID_KEY]) || !is_null($this->programId))) {
            throw new InvalidArgumentException("To create a deal you need to specify ProgramId parameter");
        }
        parent::checkDataStructure($itemData);
    }

    /**
     * Sends request to Megaplan and returns created or updated entity accordingly to DataStore interface.
     *
     * @param $itemData
     * @return mixed
     * @throws InvalidArgumentException
     * @throws \Exception
     */
    protected function put($itemData)
    {
        $itemData = $this->prepareDataStructure($itemData);
        $this->checkDataStructure($itemData);
        if (!isset($itemData[static::PROGRAM_ID_KEY])) {
            $itemData[static::PROGRAM_ID_KEY] = $this->programId;
        }

        $response = $this->megaplanClient->get('/BumsTradeApiV01/Deal/save.api', $itemData);
        $data = $this->serializer->unserialize($response);

        $dealId = $data[static::ID_OPTION_KEY];
        $this->setId($dealId);
        return $this->get();
    }

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     * @throws InvalidArgumentException
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
     * {@inheritdoc}
     *
     * {@inheritdoc}
     * @throws InvalidArgumentException
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
}