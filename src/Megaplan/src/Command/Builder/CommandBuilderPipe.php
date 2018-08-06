<?php


namespace rollun\api\megaplan\Command\Builder;


use rollun\api\megaplan\Command\CommandInterface;
use rollun\api\megaplan\Exception\InvalidCommandType;

class CommandBuilderPipe implements CommandBuilderInterface
{

    /**
     * @var CommandBuilderInterface[]
     */
    private $commandBuilders;

    /**
     * CommandBuilderPipe constructor.
     * @param array $commandBuilders
     */
    public function __construct(array $commandBuilders)
    {
        foreach ($this->commandBuilders as $commandBuilder) {
            if(!$commandBuilder instanceof CommandBuilderInterface) {
                throw new \InvalidArgumentException(
                    "Array must contains only " . CommandBuilderInterface::class . " objects, instead " . gettype($commandBuilders));
            }
        }
        $this->commandBuilders = $commandBuilders;
    }

    /**
     * @param string $commandType
     * @param mixed ...$args
     * @return CommandInterface
     * @throws InvalidCommandType
     */
    public function build(string $commandType, ...$args): CommandInterface
    {
        foreach ($this->commandBuilders as $commandBuilder) {
            if($commandBuilder->canBuild($commandType)) {
                return $commandBuilder->build($commandType, $args);
            }
        }
        throw new \InvalidArgumentException("Command $commandType not valid.");
    }

    /**
     * @param string $commandType
     * @return bool
     */
    public function canBuild(string $commandType)
    {
        foreach ($this->commandBuilders as $commandBuilder) {
            if($commandBuilder->canBuild($commandType)) {
                return true;
            }
        }
        return false;
    }
}