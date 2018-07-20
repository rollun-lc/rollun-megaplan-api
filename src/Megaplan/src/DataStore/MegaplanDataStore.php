<?php

namespace rollun\api\megaplan\DataStore;

use rollun\api\megaplan\DataStore\ConditionBuilder\MegaplanConditionBuilder;
use rollun\api\megaplan\Entity\EntityAbstract;
use rollun\api\megaplan\Entity\ListEntityAbstract;
use rollun\api\megaplan\Entity\SingleEntityAbstract;
use rollun\datastore\DataStore\DataStoreAbstract;
use rollun\datastore\DataStore\DataStoreException;
use rollun\datastore\DataStore\Interfaces\DataSourceInterface;
use Xiag\Rql\Parser\Query;

/**
 * Class MegaplanDataStore
 * @package rollun\api\megaplan\DataStore
 */
class MegaplanDataStore extends DataStoreAbstract implements DataSourceInterface
{
    const DEF_ID = 'Id';

    /** @var SingleEntityAbstract */
    protected $singleEntity;

    /** @var ListEntityAbstract */
    protected $listEntity;

    /**
     * TODO: fixed received entity type in __constructor.
     * MegaplanDataStore constructor.
     * @param SingleEntityAbstract $singleEntity
     * @param ListEntityAbstract $listEntity
     */
    public function __construct(SingleEntityAbstract $singleEntity, ListEntityAbstract $listEntity)
    {
        $this->singleEntity = $singleEntity;
        $this->listEntity = $listEntity;
        $this->conditionBuilder = new MegaplanConditionBuilder();
    }

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    public function read($id)
    {
        $this->singleEntity->setId($id);
        return $this->singleEntity->get();
    }

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    public function getAll()
    {
        return $this->listEntity->get();
    }

    /**
     * {@inheritdoc}
     * {@inheritdoc}
     */
    public function query(Query $query)
    {
        // TODO: not all listEntity support query method. Add check
        $condition = $this->conditionBuilder->__invoke($query->getQuery());
        return $this->listEntity->query($condition);
    }

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    public function create($itemData, $rewriteIfExist = false)
    {
        return $this->singleEntity->create($itemData, $rewriteIfExist);
    }

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    public function update($itemData, $createIfAbsent = false)
    {
        return $this->singleEntity->update($itemData, $createIfAbsent);
    }

    /**
     * {@inheritdoc}
     *
     * {@inheritdoc}
     */
    public function delete($id)
    {
        throw new DataStoreException("This functionality is not implemented yet");
    }
}