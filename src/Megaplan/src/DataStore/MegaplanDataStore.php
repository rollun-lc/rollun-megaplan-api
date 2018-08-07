<?php


namespace rollun\api\megaplan\DataStore;

use rollun\api\megaplan\Command\AbstractMegaplanCommand;
use rollun\api\megaplan\Command\Builder\CommandBuilderInterface;
use rollun\api\megaplan\Command\CreateEntityMegaplanCommand;
use rollun\api\megaplan\Command\UpdateEntityMegaplanCommand;
use rollun\api\megaplan\Exception\InvalidCommandType;
use rollun\datastore\DataStore\DataStoreException;
use rollun\datastore\DataStore\Interfaces\DataStoresInterface;
use rollun\datastore\DataStore\Traits\NoSupportDeleteAllTrait;
use rollun\datastore\DataStore\Traits\NoSupportDeleteTrait;

/**
 * Class MegaplanDataStore
 * @package rollun\api\megaplan\DataStore
 */
class MegaplanDataStore extends MegaplanReadStore implements DataStoresInterface
{
    use NoSupportDeleteAllTrait;
    use NoSupportDeleteTrait;

    private $changeEntityUri;

    /**
     * MegaplanDataStore constructor.
     * @param CommandBuilderInterface $megaplanCommandBuilder
     * @param MegaplanEntityFieldsDataSource $entityFieldsDataSource
     * @param string $programId
     * @param string $getEntityUri
     * @param string $getEntitiesUri
     * @param string $changeEntityUri
     */
    public function __construct(
        CommandBuilderInterface $megaplanCommandBuilder,
        MegaplanEntityFieldsDataSource $entityFieldsDataSource,
        string $programId,
        string $getEntityUri,
        string $getEntitiesUri,
        string $changeEntityUri
    )
    {
        parent::__construct($megaplanCommandBuilder, $entityFieldsDataSource, $programId, $getEntityUri, $getEntitiesUri);
        $this->changeEntityUri = $changeEntityUri;
    }

    /**
     * By default, insert new (by create) Item.
     *
     * It can't overwrite existing item by default.
     * You can get creatad item us result this function.
     *
     * If  $itemData["id"] !== null, item set with that 'id'.
     * If item with same 'id' already exist - method will throw exception,
     * but if $rewriteIfExist = true item will be rewrited.<br>
     *
     * If $itemData["id"] is not set or $itemData["id"]===null,
     * item will be insert with autoincrement PrimryKey.<br>
     *
     * @param array $itemData associated array with or without PrimaryKey ["id" => 1, "field name" = "foo" ]
     * @param bool $rewriteIfExist can item be rewrited if same 'id' exist
     * @return array created item or method will throw exception
     */
    public function create($itemData, $rewriteIfExist = false)
    {
        try {
            $itemData[CreateEntityMegaplanCommand::KEY_PROGRAM_ID] = $this->programId;
            $command = $this->megaplanCommandBuilder->build(
                CreateEntityMegaplanCommand::class,
                $this->changeEntityUri,
                $itemData,
                $this->entityFieldsDataSource->getExtraFields()
            );
            return $command->execute();
        } catch (InvalidCommandType $e) {
            throw new DataStoreException("Buy create entity get exception - {$e->getMessage()}", $e->getCode(), $e);
        }
    }

    /**
     * By default, update existing Item.
     *
     * If item with PrimaryKey == $itemData["id"] is existing in the store, item will update.
     * A fields wich don't present in $itemData will  not be changed in item in the store.<br>
     * This method return updated item<br>
     * <br>
     * If $item["id"] isn't set - the method will throw exception.<br>
     * <br>
     * If item with PrimaryKey == $itemData["id"] is absent in the store - method  will throw exception,<br>
     * but if $createIfAbsent = true item will be created and this method return inserted item<br>
     * <br>
     *
     * @param array $itemData associated array with PrimaryKey ["id" => 1, "field name" = "foo" ]
     * @param bool $createIfAbsent can item be created if same 'id' is absent in the store
     * @return array updated or inserted item.
     */
    public function update($itemData, $createIfAbsent = false)
    {
        try {
            $itemData[CreateEntityMegaplanCommand::KEY_PROGRAM_ID] = $this->programId;
            $command = $this->megaplanCommandBuilder->build(
                UpdateEntityMegaplanCommand::class,
                $this->changeEntityUri,
                $itemData,
                $this->entityFieldsDataSource->getExtraFields()
            );
            return $command->execute();
        } catch (InvalidCommandType $e) {
            throw new DataStoreException("Buy update entity get exception - {$e->getMessage()}", $e->getCode(), $e);
        }
    }
}