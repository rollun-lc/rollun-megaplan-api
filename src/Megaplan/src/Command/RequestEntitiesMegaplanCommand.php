<?php


namespace rollun\api\megaplan\Command;


use rollun\api\megaplan\DataStore\ConditionBuilder\MegaplanConditionBuilder;
use rollun\api\megaplan\Exception\InvalidRequestCountException;
use rollun\api\megaplan\MegaplanClient;
use rollun\api\megaplan\Traits\CustomFieldMappingTrait;
use rollun\datastore\DataStore\Interfaces\ReadInterface;
use Xiag\Rql\Parser\Query;

class RequestEntitiesMegaplanCommand extends AbstractMegaplanCommand
{

    use CustomFieldMappingTrait;

    /**
     * The Megaplan API allows send requests not more than this limit per hour
     */
    const MAX_REQUEST_COUNT_PER_HOUR = 3000;

    /**
     *
     */
    const INF_LIMIT = 2147483647;

    /**
     * The Megaplan API allows to get rows count not more than this limit per request
     */
    const MAX_LIMIT = 100;
    /**
     * [
     *   'FilterFields' => [],
     *   'RequestedFields' => [],
     *   'ExtraFields' => [],
     *   'Limit' => self::MAX_LIMIT,
     *   'Offset' =>
     * ]
     *
     * @var array
     */
    private $requestParams = [
        'FilterFields' => [],
        'RequestedFields' => [],
        'ExtraFields' => [],
        'Limit' => self::MAX_LIMIT,
        'Offset' => 0,
    ];

    /**
     * QueryEntitiesMegaplanCommand constructor.
     * @param MegaplanClient $megaplanClient
     * @param string $uri
     * @param array $requestParams
     */
    public function __construct(MegaplanClient $megaplanClient, string $uri, array $requestParams)
    {
        parent::__construct($megaplanClient, $uri);
        $this->requestParams = array_merge($this->requestParams, $requestParams);
    }

    /**
     * @return array
     * @throws InvalidRequestCountException
     */
    public function execute()
    {
        $data = [];
        $requestCount = 0;
        $limit = $this->requestParams["Limit"];
        $this->requestParams["Limit"] = $limit > static::MAX_LIMIT ? static::MAX_LIMIT : $limit;
        do {
            $partData = $this->megaplanClient->get($this->uri, $this->getRequestParams());
            $data = array_merge($data, $partData);

            // check if the limit is exceeded
            $requestCount++;
            if ($requestCount >= static::MAX_REQUEST_COUNT_PER_HOUR) {
                throw new InvalidRequestCountException("The limit of requests per hour is exceeded");
            }
            // delay
            usleep($this->getRequestInterval());
            // get the next entities
            $offset = $limit - count($data);
            $this->requestParams['Offset'] += ($offset > static::MAX_LIMIT ? static::MAX_LIMIT : $offset);
            // do this while the last part of entities is less than 100 - in this case we reach end of the entities list
            //count($data) < ($limit)$this->requestParams['Limit']
            //count($data) == $this->requestParams['Offset']
        } while (count($data) < $limit && count($partData) == $this->requestParams["Limit"]);
        $this->reset();

        return array_map([$this, 'customFieldMap'], $data);
    }


    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    protected function getRequestParams()
    {
        return $this->requestParams;
    }

    /**
     * Resets offset and rebuilds filter fields with the new conditions
     */
    protected function reset()
    {
        $this->requestParams['Offset'] = 0;
    }

    /**
     * The Megaplan API has limit of requests per hour.
     * So when we get the list of the entities we have to check if we don't exceed this limit.
     * That's why we send requests in equal time intervals.
     *
     * @return float
     */
    protected function getRequestInterval()
    {
        return ceil(3600 / self::MAX_REQUEST_COUNT_PER_HOUR * 1000);
    }
}