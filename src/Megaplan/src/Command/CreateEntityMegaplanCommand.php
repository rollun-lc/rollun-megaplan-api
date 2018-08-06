<?php


namespace rollun\api\megaplan\Command;

use rollun\api\megaplan\Exception\InvalidArgumentException;

/**
 * Class CreateEntityMegaplanCommand
 * @package rollun\api\megaplan\Command
 */
class CreateEntityMegaplanCommand extends AbstractPutMegaplanCommand
{

    /**
     * @param $itemData
     * @return void
     * @throws InvalidArgumentException
     */
    protected function checkDataStructure($itemData)
    {
        if(!isset($itemData[static::KEY_PROGRAM_ID])) {
            throw new InvalidArgumentException("To create a entity you need to specify ProgramId parameter");
        }
    }
}