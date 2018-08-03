<?php


namespace rollun\api\megaplan\DataStore;


use Megaplan\SimpleClient\Client;
use rollun\api\megaplan\DataStore\ConditionBuilder\MegaplanConditionBuilder;
use rollun\datastore\DataStore\ConditionBuilder\ConditionBuilderAbstract;
use rollun\datastore\DataStore\DataStoreException;
use rollun\datastore\DataStore\Interfaces\DataStoresInterface;
use rollun\datastore\DataStore\Traits\NoSupportDeleteAllTrait;
use rollun\datastore\DataStore\Traits\NoSupportDeleteTrait;
use rollun\datastore\Rql\RqlParser;
use Xiag\Rql\Parser\Query;
use Zend\Serializer\Serializer;

/**
 * Class AbstractMegaplanAdapter realisation datastore to megaplan api.
 * Use megaplan api v1
 * @see https://dev.megaplan.ru/api/index.html
 * @package rollun\api\megaplan\DataStore
 */
abstract class AbstractMegaplanAdapter implements DataStoresInterface
{
    use NoSupportDeleteAllTrait;
    use NoSupportDeleteTrait;

    const PROGRAM_ID_KEY = "Program";

    const ID_OPTION_KEY = "Id";

    /**
     * @var Client
     */
    private $megaplanClient;

    /**
     * @var MegaplanConditionBuilder
     */
    private $conditionBuilder;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var string
     */
    private $programId;

    /**
     * @var string
     */
    private $readEntityUrl;

    /**
     * @var string
     */
    private $queryEntitiesUrl;

    /**
     * @var string
     */
    private $createEntityUrl;

    /**
     * @var string
     */
    private $updateEntityUrl;

    /**
     * @param int|string $id
     * @return mixed
     */
    public function read($id)
    {
        try {
            $response = $this->megaplanClient->get($this->readEntityUrl, [
                self::ID_OPTION_KEY => $id,
                'RequestedFields' => $this->getRequestFields(),
                'ExtraFields' => $this->getExtraFields(),
            ]);
            // Fetch data from response
            $data = $this->serializer->unserialize($response);
            return $data;
        } catch (\Throwable $throwable) {
            throw new DataStoreException(
                "By read {$id} get error - {$throwable->getMessage()}.",
                $throwable->getCode(),
                $throwable
            );
        }
    }

    /**
     * @param Query $query
     * @return array[]
     * @throws DataStoreException
     */
    public function query(Query $query)
    {
        try {
            $condition = $this->conditionBuilder->__invoke($query->getQuery());
            $response = $this->megaplanClient->get($this->queryEntitiesUrl, (array)$condition);
            return $this->serializer->unserialize($response);
        } catch (\Throwable $throwable) {
            $rql = RqlParser::rqlEncode($query);
            throw new DataStoreException(
                "By handle query {$rql} get error - {$throwable->getMessage()}.",
                $throwable->getCode(),
                $throwable
            );
        }
    }

    /**
     * @param array $itemData
     * @param bool $rewriteIfExist
     * @return mixed
     * @throws DataStoreException
     */
    public function create($itemData, $rewriteIfExist = false)
    {
        try {
            if($rewriteIfExist) {
                throw new DataStoreException("Can't rewrite if exist.");
            }

            if (!isset($itemData[static::PROGRAM_ID_KEY])) {
                $itemData[static::PROGRAM_ID_KEY] = $this->programId;
            }
            $response = $this->megaplanClient->get($this->updateEntityUrl, $itemData);
            $data = $this->serializer->unserialize($response);

            $id = $data[static::ID_OPTION_KEY];
            return $this->read($id);
        } catch (\Throwable $throwable) {
            $string = print_r($itemData, true);
            throw new DataStoreException(
                "By create entity [$string] get error - {$throwable->getMessage()}.",
                $throwable->getCode(),
                $throwable
            );
        }
    }

    /**
     * @param array $itemData
     * @param bool $createIfAbsent
     * @return array
     */
    public function update($itemData, $createIfAbsent = false)
    {
        try {
            if($createIfAbsent) {
                throw new DataStoreException("Can't create if not exist.");
            }
            if (!isset($itemData[static::PROGRAM_ID_KEY])) {
                $itemData[static::PROGRAM_ID_KEY] = $this->programId;
            }
            $response = $this->megaplanClient->get($this->createEntityUrl, $itemData);
            $data = $this->serializer->unserialize($response);

            $id = $data[static::ID_OPTION_KEY];
            return $this->read($id);
        } catch (\Throwable $throwable) {
            $string = print_r($itemData, true);
            throw new DataStoreException(
                "By update entity [$string] get error - {$throwable->getMessage()}.",
                $throwable->getCode(),
                $throwable
            );
        }
    }

    /**
     *
     */
    abstract protected function getRequestFields();

    /**
     *
     */
    abstract protected function getExtraFields();
}