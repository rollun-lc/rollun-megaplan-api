<?php


namespace rollun\api\megaplan\Command;

use rollun\api\megaplan\Command\AbstractPutMegaplanCommand;
use rollun\api\megaplan\Exception\InvalidArgumentException;

class UpdateOfferMegaplanCommand extends AbstractPutMegaplanCommand
{

    /**
     * @param $itemData
     * @return void
     * @throws InvalidArgumentException
     */
    protected function checkDataStructure($itemData)
    {
        unset($itemData[static::KEY_PROGRAM_ID]);
        if (!isset($itemData[static::KEY_ID])
        ) {
            throw new InvalidArgumentException("To update a entity you need to specify ProgramId parameter and id");
        }
    }

    public function execute()
    {
        $requestParams = $this->getRequestParams();
        return $this->megaplanClient->get($this->uri, $requestParams);
    }
}