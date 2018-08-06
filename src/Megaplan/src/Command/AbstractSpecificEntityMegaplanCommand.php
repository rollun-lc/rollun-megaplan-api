<?php


namespace rollun\api\megaplan\Command;


use rollun\api\megaplan\MegaplanClient;

abstract class AbstractSpecificEntityMegaplanCommand extends AbstractMegaplanCommand
{
    /**
     * @var string
     */
    protected $id;

    /**
     * ReadEntityMegaplanCommand constructor.
     * @param MegaplanClient $megaplanClient
     * @param string $uri
     * @param string $id
     */
    public function __construct(MegaplanClient $megaplanClient, string $uri, string $id)
    {
        parent::__construct($megaplanClient, $uri);
        $this->id = $id;
    }

    /**
     * @return array
     */
    protected function getRequestParams()
    {
        return [
            static::KEY_ID => $this->id
        ];
    }
}