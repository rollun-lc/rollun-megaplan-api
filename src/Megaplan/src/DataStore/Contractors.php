<?php


namespace rollun\api\megaplan\DataStore;


use rollun\api\megaplan\Command\Builder\CommandBuilderInterface;

class Contractors extends AbstractMegaplanEntity
{
    /**
     *
     */
    const GET_ENTITY_URI = "/BumsCrmApiV01/Contractor/card.api";

    /**
     *
     */
    const GET_ENTITIES_URI = "/BumsCrmApiV01/Contractor/list.api";

    /**
     *
     */
    const CHANGE_ENTITY_URI = "/BumsCrmApiV01/Contractor/save.api";

    /**
     *
     */
    const GET_FIELDS_URI = "/BumsCrmApiV01/Contractor/listFields.api";

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
        $entityFieldsDataSource = new ContractorEntityFieldsDataSource();
        parent::__construct(
            $megaplanCommandBuilder,
            $programId,
            $entityFieldsDataSource
        );
    }

}