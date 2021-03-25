<?php


namespace rollun\api\megaplan\Callback;


use Psr\Log\LoggerInterface;
use rollun\api\megaplan\Factory\EntityFactoryAbstract;
use rollun\api\megaplan\MegaplanClient;
use rollun\dic\InsideConstruct;

class MegaplanCallback
{
    protected $callback;

    protected $factory;

    protected $logger;

    public function __construct(
        callable $callback,
        EntityFactoryAbstract $factory,
        LoggerInterface $logger
    ) {
        $this->callback = $callback;
        $this->factory = $factory;
        $this->logger = $logger;
    }

    public function __invoke($data = null)
    {
        $this->logger->debug('Got request from megaplan', [
            'data' => $data
        ]);

        $entity = $this->factory->createInstance($data);

        ($this->callback)($entity);
    }

    public function __sleep()
    {
        return [
            'callback',
            'factory'
        ];
    }

    public function __wakeup()
    {
        InsideConstruct::initWakeup([
            'logger' => LoggerInterface::class,
        ]);
    }
}