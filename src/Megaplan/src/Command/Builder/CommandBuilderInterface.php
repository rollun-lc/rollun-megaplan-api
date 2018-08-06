<?php


namespace rollun\api\megaplan\Command\Builder;


use rollun\api\megaplan\Command\CommandInterface;
use rollun\api\megaplan\Exception\InvalidCommandType;

/**
 * Interface CommandBuilderInterface
 * @package rollun\api\megaplan\Command\Builder
 */
interface CommandBuilderInterface
{
    /**
     * @param string $commandType
     * @param mixed ...$args
     * @return CommandInterface
     * @throws InvalidCommandType
     */
    public function build(string $commandType, ...$args): CommandInterface;

    /**
     * @param string $commandType
     * @return bool
     */
    public function canBuild(string $commandType);
}