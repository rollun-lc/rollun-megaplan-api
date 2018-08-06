<?php


namespace rollun\api\megaplan\Command;

use rollun\api\megaplan\Exception\InvalidArgumentException;

/**
 * Class CreateEntityMegaplanCommand
 * @package rollun\api\megaplan\Command
 */
class UpdateEntityMegaplanCommand extends AbstractPutMegaplanCommand
{

    /**
     * @param $itemData
     * @return void
     * @throws InvalidArgumentException
     */
    protected function checkDataStructure($itemData)
    {
        if (!isset($itemData[static::KEY_PROGRAM_ID]) ||
            !isset($itemData[static::KEY_ID])
        ) {
            throw new InvalidArgumentException("To update a entity you need to specify ProgramId parameter and id");
        }
    }
}