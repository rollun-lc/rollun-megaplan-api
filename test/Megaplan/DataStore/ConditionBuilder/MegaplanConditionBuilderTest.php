<?php

namespace rollun\test\api\megaplan\DataStore\ConditionBuilder;

use PHPUnit\Framework\TestCase;
use rollun\api\megaplan\DataStore\ConditionBuilder\MegaplanConditionBuilder;
use Xiag\Rql\Parser\Query;
use Xiag\Rql\Parser\Node\Query\ScalarOperator;
use Xiag\Rql\Parser\Node\Query\LogicOperator;
use rollun\api\megaplan\Exception\InvalidArgumentException;

class MegaplanConditionBuilderTest extends TestCase
{
    /** @var MegaplanConditionBuilder */
    protected $conditionBuilder;

    protected function setUp()
    {
        $this->conditionBuilder = new MegaplanConditionBuilder();
    }

    public function test_prohibitionOfSelectionById()
    {
        $query = new Query();
        $node = new ScalarOperator\EqNode("Id", 1);
        $query->setQuery($node);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The selection for field \"Id\" is prohibited");

        $this->conditionBuilder->__invoke($query->getQuery());
    }

    public function test_eq()
    {
        $query = new Query();
        $node = new ScalarOperator\EqNode("field", 1);
        $query->setQuery($node);

        $condition = $this->conditionBuilder->__invoke($query->getQuery());

        $this->assertEquals(
            [
                "field" => 1,
            ],
            $condition
        );
    }

    public function test_ne()
    {
        $query = new Query();
        $node = new ScalarOperator\NeNode("field", 1);
        $query->setQuery($node);
        $condition = $this->conditionBuilder->__invoke($query->getQuery());

        $this->assertEquals(
            [
                'not' => [
                    ["field" => 1,],
                ],
            ],
            $condition
        );
    }

    public function test_ge()
    {
        $query = new Query();
        $node = new ScalarOperator\GeNode("field", 1);
        $query->setQuery($node);

        $condition = $this->conditionBuilder->__invoke($query->getQuery());

        $this->assertEquals(
            [
                "field" => [
                    "greaterOrEqual" => 1,
                ],
            ],
            $condition
        );
    }

    public function test_gt()
    {
        $query = new Query();
        $node = new ScalarOperator\GtNode("field", 1);
        $query->setQuery($node);

        $condition = $this->conditionBuilder->__invoke($query->getQuery());

        $this->assertEquals(
            [
                "field" => [
                    "greater" => 1,
                ],
            ],
            $condition
        );
    }

    public function test_le()
    {
        $query = new Query();
        $node = new ScalarOperator\LeNode("field", 1);
        $query->setQuery($node);

        $condition = $this->conditionBuilder->__invoke($query->getQuery());

        $this->assertEquals(
            [
                "field" => [
                    "lessOrEqual" => 1,
                ],
            ],
            $condition
        );
    }

    public function test_lt()
    {
        $query = new Query();
        $node = new ScalarOperator\LtNode("field", 1);
        $query->setQuery($node);

        $condition = $this->conditionBuilder->__invoke($query->getQuery());

        $this->assertEquals(
            [
                "field" => [
                    "less" => 1,
                ],
            ],
            $condition
        );
    }

    public function test_not()
    {
        $query = new Query();
        $node = new LogicOperator\NotNode([
            new ScalarOperator\NeNode("Category1000051CustomFieldPostavshchik", 'Rocky Mountain')
        ]);
        $query->setQuery($node);

        $condition = $this->conditionBuilder->__invoke($query->getQuery());

        $this->assertEquals(
            [
                ['not' => [
                        'not' => [
                            ['Category1000051CustomFieldPostavshchik' => 'Rocky Mountain',],
                        ],
                    ],
                ],
            ],
            $condition
        );
    }

    public function test_or()
    {
        $query = new Query();
        $node = new LogicOperator\OrNode([
            new ScalarOperator\EqNode("Category1000051CustomFieldPostavshchik", 'Mid-USA'),
            new ScalarOperator\EqNode("Category1000051CustomFieldPostavshchik", 'RLM'),
        ]);
        $query->setQuery($node);

        $condition = $this->conditionBuilder->__invoke($query->getQuery());

        $this->assertEquals(
            [
                ['or' => [
                        ['Category1000051CustomFieldPostavshchik' => 'Mid-USA',],
                        ['Category1000051CustomFieldPostavshchik' => 'RLM',],
                    ],
                ],
            ],
            $condition
        );
    }

    public function test_and()
    {
        $query = new Query();
        $node = new LogicOperator\AndNode([
            new ScalarOperator\EqNode("Category1000051CustomFieldPostavshchik", 'Mid-USA'),
            new ScalarOperator\EqNode("Program", 6),
        ]);
        $query->setQuery($node);

        $condition = $this->conditionBuilder->__invoke($query->getQuery());

        $this->assertEquals(
            [
                ['and' => [
                        ['Category1000051CustomFieldPostavshchik' => 'Mid-USA',],
                        ['Program' => 6,],
                    ],
                ],
            ],
            $condition
        );
    }

    public function test_compoundQuery1()
    {
        $query = new Query();
        $node = new LogicOperator\AndNode([
            new ScalarOperator\EqNode("Program", 6),
            new LogicOperator\OrNode([
                new ScalarOperator\EqNode("Category1000051CustomFieldPostavshchik", 'Mid-USA'),
                new ScalarOperator\EqNode("Category1000051CustomFieldPostavshchik", 'RLM'),
            ]),
            new ScalarOperator\NeNode("Name", "№32"),

        ]);
        $query->setQuery($node);

        $condition = $this->conditionBuilder->__invoke($query->getQuery());

        $this->assertEquals(
            [
                ['and' => [
                    ['Program' => '6',],
                    [
                        ['or' =>
                            [
                                ['Category1000051CustomFieldPostavshchik' => 'Mid-USA',],
                                ['Category1000051CustomFieldPostavshchik' => 'RLM',],
                            ],
                        ],
                    ],
                    ['not' =>
                        [
                            ['Name' => '№32',],
                        ],
                    ],],
                ],
            ],
            $condition
        );
    }

    public function test_compoundQuery2()
    {
        $query = new Query();
        $node = new LogicOperator\AndNode([
            new ScalarOperator\EqNode("Program", 6),
            new LogicOperator\NotNode([
                new ScalarOperator\EqNode("Category1000051CustomFieldPostavshchik", 'Rocky Mountain'),
            ]),
            new ScalarOperator\GeNode("TimeUpdated", "2017-09-07 00:00:00"),
        ]);
        $query->setQuery($node);

        $condition = $this->conditionBuilder->__invoke($query->getQuery());

        $this->assertEquals(
            [
                ['and' =>
                    [
                        ['Program' => '6',],
                        [
                            ['not' =>
                                ['Category1000051CustomFieldPostavshchik' => 'Rocky Mountain',],
                            ],
                        ],
                        ['TimeUpdated' =>
                            ['greaterOrEqual' => '2017-09-07 00:00:00',],
                        ],
                    ],
                ],
            ],
            $condition
        );
    }
}