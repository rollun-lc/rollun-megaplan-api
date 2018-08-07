<?php


namespace rollun\api\megaplan\Command\Builder;

use rollun\api\megaplan\Command\AbstractMegaplanCommand;
use rollun\api\megaplan\Command\CommandInterface;

/**
 * Build megaplan command
 * Class MegaplanCommandBuilder
 * @package rollun\api\megaplan\Command
 */
class MegaplanCommandBuilderAbstract extends AbstractMegaplanCommandBuilder
{

    /**
     * @param string $commandType
     * @param mixed ...$args
     * @return mixed
     */
    public function build(string $commandType, ...$args): CommandInterface
    {
        if (!class_exists($commandType)) {
            throw new \InvalidArgumentException("Command $commandType not found.");
        }
        if(!is_a($commandType, AbstractMegaplanCommand::class, true)) {
            throw new \InvalidArgumentException("Command $commandType not valid.");
        }
        return new $commandType($this->megaplanClient, ...$args);
    }

    /**
     * @param string $commandType
     * @return bool
     */
    public function canBuild(string $commandType)
    {
        return (
            class_exists($commandType) &&
            is_a($commandType, AbstractMegaplanCommand::class, true)
        );
    }
}