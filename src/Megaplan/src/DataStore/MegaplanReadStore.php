<?php


namespace rollun\api\megaplan\DataStore;


use Psr\Log\InvalidArgumentException;
use rollun\api\megaplan\Command\AbstractMegaplanCommand;
use rollun\api\megaplan\Command\Builder\CommandBuilderInterface;
use rollun\api\megaplan\Command\Builder\RequestByQueryMegaplanCommandBuilder;
use rollun\api\megaplan\Command\RequestEntitiesMegaplanCommand;
use rollun\api\megaplan\Command\ReadEntityMegaplanCommand;
use rollun\api\megaplan\DataStore\ConditionBuilder\MegaplanConditionBuilder;
use rollun\api\megaplan\Exception\InvalidCommandType;
use rollun\datastore\DataStore\ConditionBuilder\ConditionBuilderAbstract;
use rollun\datastore\DataStore\DataStoreException;
use rollun\datastore\DataStore\Interfaces\DataSourceInterface;
use rollun\datastore\DataStore\Interfaces\DataStoresInterface;
use rollun\datastore\DataStore\Interfaces\ReadInterface;
use rollun\datastore\DataStore\Traits\NoSupportCountTrait;
use rollun\datastore\DataStore\Traits\NoSupportIteratorTrait;
use rollun\datastore\Rql\RqlParser;
use Traversable;
use Xiag\Rql\Parser\Node\Query\LogicOperator\AndNode;
use Xiag\Rql\Parser\Node\Query\LogicOperator\OrNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\EqNode;
use Xiag\Rql\Parser\Query;

/**
 * Class ReadMegaplan
 * @package rollun\api\megaplan\DataStore
 */
class MegaplanReadStore implements ReadInterface
{

    use NoSupportCountTrait;
    use NoSupportIteratorTrait;


    /**
     * @var CommandBuilderInterface
     */
    protected $megaplanCommandBuilder;

    /**
     * @var EntityFieldsDataSourceInterface
     */
    protected $entityFieldsDataSource;

    /**
     * @var string
     */
    protected $programId;

    /**
     * @var string
     */
    private $getEntityUri;

    /**
     * @var string
     */
    private $getEntitiesUri;

    /**
     * AbstractMegaplanReadStore constructor.
     * @param CommandBuilderInterface $megaplanCommandBuilder
     * @param EntityFieldsDataSourceInterface $entityFieldsDataSource
     * @param string $programId
     * @param string $getEntityUri
     * @param string $getEntitiesUri
     */
    public function __construct(
        CommandBuilderInterface $megaplanCommandBuilder,
        EntityFieldsDataSourceInterface $entityFieldsDataSource,
        string $programId,
        string $getEntityUri,
        string $getEntitiesUri
    )
    {
        $this->megaplanCommandBuilder = $megaplanCommandBuilder;
        $this->entityFieldsDataSource = $entityFieldsDataSource;
        $this->programId = $programId;
        $this->getEntityUri = $getEntityUri;
        $this->getEntitiesUri = $getEntitiesUri;
    }

    /**
     * Return primary key identifier
     *
     * Return "id" by default
     *
     * @see DEF_ID
     * @return string "id" by default
     */
    public function getIdentifier()
    {
        return AbstractMegaplanCommand::KEY_ID;
    }

    /**
     * Return Item by 'id'
     *
     * Method return null if item with that id is absent.
     * Format of Item - Array("id"=>123, "field1"=value1, ...)
     *
     * @param int|string $id PrimaryKey
     * @return array|null
     */
    public function read($id)
    {
        try {
            $command = $this->megaplanCommandBuilder
                ->build(
                    ReadEntityMegaplanCommand::class,
                    $this->getEntityUri,
                    $id,
                    [],
                    $this->entityFieldsDataSource->getExtraFields()
                );
            return $command->execute();
        } catch (InvalidCommandType $e) {
            throw new DataStoreException("Get exception by read entity with $id.", $e->getCode(), $e);
        }
    }

    /**
     * Return true if item with that 'id' is present.
     *
     * @param int|string $id PrimaryKey
     * @return bool
     */
    public function has($id)
    {
        return !is_null($this->read($id));
    }

    /**
     * Return items by criteria with mapping, sorting and paging
     *
     * Example:
     * <code>
     *  $query = new \Xiag\Rql\Parser\Query();
     *  $eqNode = new \Xiag\Rql\Parser\Node\ScalarOperator\EqNode(
     *      'fString', 'val2'
     *  );
     *  $query->setQuery($eqNode);
     *  $sortNode = new \Xiag\Rql\Parser\Node\Node\SortNode(['id' => '1']);
     *  $query->setSort($sortNode);
     *  $selectNode = new \Xiag\Rql\Parser\Node\Node\SelectNode(['fFloat']);
     *  $query->setSelect($selectNode);
     *  $limitNode = new \Xiag\Rql\Parser\Node\Node\LimitNode(2, 1);
     *  $query->setLimit($limitNode);
     *  $queryArray = $this->object->query($query);
     * </code>
     *
     *
     * ORDER
     * http://www.simplecoding.org/sortirovka-v-mysql-neskolko-redko-ispolzuemyx-vozmozhnostej.html
     * http://ru.php.net/manual/ru/function.usort.php
     *
     * @param Query $query
     * @return array[] fo items or [] if not any
     */
    public function query(Query $query)
    {
        try {
            $this->addRequiredNodes($query);
            $command = $this->megaplanCommandBuilder
                ->build(
                    RequestByQueryMegaplanCommandBuilder::COMMAND_TYPE,
                    $this->getEntitiesUri,
                    $this->entityFieldsDataSource,
                    $query
                );
            return $command->execute();
        } catch (InvalidCommandType $e) {
            $rql = RqlParser::rqlEncode($query);
            throw new DataStoreException("Get exception by query entities - $rql.", $e->getCode(), $e);
        }
    }

    /**
     * Create required nodes
     *
     * @param Query $query
     */
    protected function addRequiredNodes(Query &$query) {
        $node = new EqNode(ReadEntityMegaplanCommand::KEY_PROGRAM, $this->programId);
        if ($queryNode = $query->getQuery()) {
            $node = new AndNode([$queryNode, $node]);
        }
        $query->setQuery($node);
    }
}