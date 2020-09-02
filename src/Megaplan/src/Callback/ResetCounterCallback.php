<?php


namespace rollun\api\megaplan\Callback;


use Psr\Log\LoggerInterface;
use rollun\dic\InsideConstruct;
use rollun\logger\Writer\PrometheusWriter;

class ResetCounterCallback
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __wakeup()
    {
        InsideConstruct::initWakeup([
            'logger' => LoggerInterface::class,
        ]);
    }

    public function __invoke()
    {
        $this->logger->debug('Reset megaplan request counter');

        $this->logger->notice('METRICS_COUNTER', [
            PrometheusWriter::METRIC_ID => 'megaplan_requests',
            PrometheusWriter::VALUE => 0,
            PrometheusWriter::REFRESH => true,
        ]);
    }
}