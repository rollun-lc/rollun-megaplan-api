<?php

use rollun\logger\LifeCycleToken;
use Xiag\Rql\Parser\Node\LimitNode;
use Xiag\Rql\Parser\Node\Query\LogicOperator\AndNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\EqNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\GeNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\GtNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\LeNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\LtNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\NeNode;

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

/** @var \Psr\Container\ContainerInterface $container */
$container = require 'config/container.php';
\rollun\dic\InsideConstruct::setContainer($container);
$lifeCycleToke = LifeCycleToken::generateToken();
$container->setService(LifeCycleToken::class, $lifeCycleToke);

/**
 * @var \rollun\api\megaplan\DataStore\Deals $dealsDataStore
 */
$dealsDataStore = $container->get("Deals-13");
$query = new \Xiag\Rql\Parser\Query();
$query->setLimit(new LimitNode(1));
$result = $dealsDataStore->query($query);
print_r($result);

/**
 * @var \rollun\api\megaplan\DataStore\Contractors $contractorsDataStore
 */
$contractorsDataStore = $container->get("Contractor-13");
/*$query = new \Xiag\Rql\Parser\Query();
$query->setLimit(new LimitNode(1));
$result = $contractorsDataStore->query($query);
print_r($result);
*/
