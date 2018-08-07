<?php

use rollun\logger\LifeCycleToken;
use Xiag\Rql\Parser\Node\LimitNode;

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


