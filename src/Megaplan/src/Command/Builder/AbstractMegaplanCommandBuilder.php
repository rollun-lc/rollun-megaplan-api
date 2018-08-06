<?php


namespace rollun\api\megaplan\Command\Builder;


use rollun\api\megaplan\MegaplanClient;

abstract class AbstractMegaplanCommandBuilder implements CommandBuilderInterface
{
    /**
     * @var MegaplanClient
     */
    protected $megaplanClient;

    /**
     * MegaplanCommandBuilder constructor.
     * @param MegaplanClient $megaplanClient
     */
    public function __construct(MegaplanClient $megaplanClient)
    {
        $this->megaplanClient = $megaplanClient;
    }

}