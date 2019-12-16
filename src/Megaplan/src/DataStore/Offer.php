<?php


namespace rollun\api\megaplan\DataStore;


use rollun\api\megaplan\Command\Builder\CommandBuilderInterface;
use rollun\api\megaplan\Command\CreateOfferMegaplanCommand;
use rollun\api\megaplan\Command\UpdateOfferMegaplanCommand;
use rollun\api\megaplan\Exception\InvalidCommandType;
use rollun\datastore\DataStore\DataStoreException;

/**
 * Class Offer
 * @package rollun\api\megaplan\DataStore
 * @see https://dev.megaplan.ru/api/API_invoices.html#id20
 */
class Offer extends AbstractMegaplanEntity
{
    /**
     *
     */
    public const GET_ENTITIES_URI = '/BumsInvoiceApiV01/Offer/list.api';

    /**
     *
     */
    public const CHANGE_ENTITY_URI = '/BumsInvoiceApiV01/Offer/save.api';

    /**
     *
     */
    public const GET_FIELDS_URI = '/BumsInvoiceApiV01/Offer/listFields.api';

    /**
     * DealsDataStore constructor.
     * @param CommandBuilderInterface $megaplanCommandBuilder
     * @param EntityFieldsDataSourceInterface|null $entityFieldsDataSource
     */
    public function __construct(
        CommandBuilderInterface $megaplanCommandBuilder,
        EntityFieldsDataSourceInterface $entityFieldsDataSource = null
    ) {
        $entityFieldsDataSource = $entityFieldsDataSource ?? new ContractorEntityFieldsDataSource(
                $megaplanCommandBuilder,
                static::GET_FIELDS_URI
            );
        parent::__construct(
            $megaplanCommandBuilder,
            '',
            $entityFieldsDataSource
        );
    }

    public function create($itemData, $rewriteIfExist = false)
    {
        try {
            $command = $this->megaplanCommandBuilder->build(
                CreateOfferMegaplanCommand::class,
                self::CHANGE_ENTITY_URI,
                $itemData,
                $this->entityFieldsDataSource->getExtraFields()
            );
            return $command->execute();
        } catch (InvalidCommandType $e) {
            throw new DataStoreException("Buy create entity get exception - {$e->getMessage()}", $e->getCode(), $e);
        }
    }

    public function update($itemData, $createIfAbsent = false)
    {
        try {
            $command = $this->megaplanCommandBuilder->build(
                UpdateOfferMegaplanCommand::class,
                self::CHANGE_ENTITY_URI,
                $itemData,
                $this->entityFieldsDataSource->getExtraFields()
            );
            return $command->execute();
        } catch (InvalidCommandType $e) {
            throw new DataStoreException("Buy update entity get exception - {$e->getMessage()}", $e->getCode(), $e);
        }
    }

    public function read($id)
    {
        throw new DataStoreException('Read method not support for Offer store.');
    }


}