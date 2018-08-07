<?php


namespace rollun\api\megaplan\DataStore;


use rollun\api\megaplan\Command\Builder\CommandBuilderInterface;
use rollun\api\megaplan\Command\MegaplanCommandBuilderAbstract;
use rollun\api\megaplan\DataStore\ConditionBuilder\MegaplanConditionBuilder;

abstract class AbstractMegaplanEntity extends MegaplanDataStore
{

    /**
     *
     */
    const GET_ENTITY_URI = "";

    /**
     *
     */
    const GET_ENTITIES_URI = "";

    /**
     *
     */
    const CHANGE_ENTITY_URI = "";

    /**
     *
     */
    const GET_FIELDS_URI = "";

    /**
     * DealsDataStore constructor.
     * @param CommandBuilderInterface $megaplanCommandBuilder
     * @param string $programId
     */
    public function __construct(
        CommandBuilderInterface $megaplanCommandBuilder,
        string $programId
    )
    {
        $entityFieldsDataSource = new MegaplanEntityFieldsDataSource(
            $megaplanCommandBuilder,
            static::GET_FIELDS_URI,
            $programId
        );
        parent::__construct(
            $megaplanCommandBuilder,
            $entityFieldsDataSource,
            $programId,
            static::GET_ENTITY_URI,
            static::GET_ENTITIES_URI,
            static::CHANGE_ENTITY_URI);
    }
}