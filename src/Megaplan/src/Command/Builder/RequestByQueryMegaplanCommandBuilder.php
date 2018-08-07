<?php


namespace rollun\api\megaplan\Command\Builder;

use rollun\api\megaplan\Command\CommandInterface;
use rollun\api\megaplan\Command\RequestEntitiesMegaplanCommand;
use rollun\api\megaplan\DataStore\ConditionBuilder\MegaplanConditionBuilder;
use rollun\api\megaplan\DataStore\MegaplanEntityFieldsDataSource;
use rollun\api\megaplan\Exception\InvalidCommandType;
use rollun\api\megaplan\MegaplanClient;
use Xiag\Rql\Parser\Node\AbstractQueryNode;
use Xiag\Rql\Parser\Node\Query\AbstractLogicOperatorNode;
use Xiag\Rql\Parser\Node\Query\AbstractScalarOperatorNode;
use Xiag\Rql\Parser\Node\Query\LogicOperator\AndNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\NeNode;
use Xiag\Rql\Parser\Query;

class RequestByQueryMegaplanCommandBuilder extends AbstractMegaplanCommandBuilder
{
    const COMMAND_TYPE = "RequestByQueryMegaplanCommand";
    /**
     * @var MegaplanConditionBuilder
     */
    private $megaplanConditionBuilder;

    /**
     * RequestByQueryMegaplanCommandBuilder constructor.
     * @param MegaplanClient $megaplanClient
     * @param MegaplanConditionBuilder $megaplanConditionBuilder
     */
    public function __construct(MegaplanClient $megaplanClient, MegaplanConditionBuilder $megaplanConditionBuilder = null)
    {
        parent::__construct($megaplanClient);
        $this->megaplanConditionBuilder = $megaplanConditionBuilder ?? new MegaplanConditionBuilder();
    }

    /**
     * @param AbstractQueryNode $query
     * @param MegaplanEntityFieldsDataSource $entityFieldsDataSource
     * @return AbstractQueryNode
     * @throws InvalidCommandType
     */
    protected function rebuildQuery(AbstractQueryNode $query, MegaplanEntityFieldsDataSource $entityFieldsDataSource)
    {
        if($query instanceof AbstractScalarOperatorNode) {
            $query->setField($entityFieldsDataSource->warpField($query->getField()));
        }elseif($query instanceof AbstractLogicOperatorNode) {
            foreach ($query->getQueries() as &$childQuery) {
                $this->rebuildQuery($childQuery, $entityFieldsDataSource);
            }
        }
        return $query;
    }

    /**
     * @param string $commandType
     * @param mixed ...$args
     * @return CommandInterface
     * @throws InvalidCommandType
     */
    public function build(string $commandType, ...$args): CommandInterface
    {
        if (!$this->canBuild($commandType)) {
            throw new \InvalidArgumentException("Command $commandType not valid.");
        }

        $query = array_pop($args);
        if (!$query || !$query instanceof Query) {
            throw new \InvalidArgumentException("query not set or not valid.");
        }

        $entityFieldsDataSource = array_pop($args);
        if (!$entityFieldsDataSource || !$entityFieldsDataSource instanceof MegaplanEntityFieldsDataSource) {
            throw new \InvalidArgumentException("entityFieldsDataSource not set or not valid.");
        }
        $uri = array_pop($args);
        if (!$uri) {
            throw new \InvalidArgumentException("Uri not set.");
        }

        if($query->getQuery()) {
            $query->setQuery($this->rebuildQuery($query->getQuery(), $entityFieldsDataSource));
        }
        $requestParam["FilterFields"] = (array)$this->megaplanConditionBuilder->__invoke($query->getQuery());

        if ($query->getLimit()) {
            $requestParam["Limit"] = $query->getLimit()->getLimit();
            $requestParam["Offset"] = $query->getLimit()->getOffset();
        }

        if ($query->getSelect()) {
            $fields = $query->getSelect()->getFields();
            foreach ($fields as $field) {
                if ($entityFieldsDataSource->hasField($field)) {
                    $requestParam["RequestedFields"][] = $field;
                } elseif ($entityFieldsDataSource->hasExtraField($field)) {
                    $requestParam["ExtraFields"][] = $field;
                } else {
                    throw new \RuntimeException("Fields $field not found in entity.");
                }
            }
        } else {
            $requestParam["ExtraFields"] = $entityFieldsDataSource->getExtraFields();;
        }

        $command = new RequestEntitiesMegaplanCommand($this->megaplanClient, $uri, $requestParam);
        return $command;
    }

    /**
     * @param string $commandType
     * @return bool
     */
    public function canBuild(string $commandType)
    {
        return $commandType === static::COMMAND_TYPE;
    }
}