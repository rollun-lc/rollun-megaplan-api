<?php

namespace rollun\api\megaplan\DataStore\ConditionBuilder;

use rollun\datastore\DataStore\ConditionBuilder\ConditionBuilderAbstract;
use Xiag\Rql\Parser\Node\AbstractQueryNode;
use rollun\api\megaplan\Exception\InvalidArgumentException;

class MegaplanConditionBuilder extends ConditionBuilderAbstract
{
    protected $literals = [
        'LogicOperator' => [
            'and' => ['before' => '[{"and":[', 'between' => ',', 'after' => ']}]'],
            'or' => ['before' => '[{"or":[', 'between' => ',', 'after' => ']}]'],
            'not' => ['before' => '[{"not":', 'between' => '":"', 'after' => '}]'],
        ],
        'ScalarOperator' => [
            'eq' => ['before' => '{"', 'between' => '":"', 'after' => '"}'],
            'ne' => ['before' => '{"not":[{"', 'between' => '":"', 'after' => '"}]}'],
            'ge' => ['before' => '{"', 'between' => '":{"greaterOrEqual":"', 'after' => '"}}'],
            'gt' => ['before' => '{"', 'between' => '":{"greater":"', 'after' => '"}}'],
            'le' => ['before' => '{"', 'between' => '":{"lessOrEqual":"', 'after' => '"}}'],
            'lt' => ['before' => '{"', 'between' => '":{"less":"', 'after' => '"}}'],
        ]
    ];

    protected $illegalFieldNames = [
        'Id',
    ];

    public static function encodeString($value)
    {
        /*
         * Don't encode string
         * Return it in its view
         */
        return $value;
    }

    public function __invoke(AbstractQueryNode $rootQueryNode = null)
    {
        return json_decode(parent::__invoke($rootQueryNode), true);
    }

    public function prepareFieldName($fieldName)
    {
        if (in_array($fieldName, $this->illegalFieldNames)) {
            throw new InvalidArgumentException("The selection for field \"{$fieldName}\" is prohibited");
        }
        return parent::prepareFieldName($fieldName);
    }
}