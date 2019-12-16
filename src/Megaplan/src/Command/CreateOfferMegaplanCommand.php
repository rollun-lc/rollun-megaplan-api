<?php


namespace rollun\api\megaplan\Command;

use rollun\api\megaplan\Command\AbstractPutMegaplanCommand;
use rollun\api\megaplan\Exception\InvalidArgumentException;

class CreateOfferMegaplanCommand extends AbstractPutMegaplanCommand
{

    /**
     * @param $itemData
     * @return void
     * @throws InvalidArgumentException
     */
    protected function checkDataStructure($itemData)
    {
        unset($itemData[self::KEY_PROGRAM_ID]);
    }

    public function execute()
    {
        $requestParams = $this->getRequestParams();
        return $this->megaplanClient->get($this->uri, $requestParams);
    }
}