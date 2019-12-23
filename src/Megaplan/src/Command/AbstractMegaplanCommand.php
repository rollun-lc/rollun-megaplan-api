<?php


namespace rollun\api\megaplan\Command;

use Megaplan\SimpleClient\Client;
use rollun\api\megaplan\MegaplanClient;
use Zend\Serializer\Serializer;

/**
 * Class AbstractMegaplanCommand
 * @package rollun\api\megaplan\Comman
 */
abstract class AbstractMegaplanCommand implements CommandInterface
{
    const KEY_ID = "Id";

    const KEY_PROGRAM_ID = "ProgramId";

    const KEY_PROGRAM = 'Program';

    const KEY_GUID = "GUID";

    /**
     * @var MegaplanClient
     */
    protected $megaplanClient;

    /**
     * @var string
     */
    protected $uri;

    /**
     * AbstractMegaplanCommand constructor.
     * @param MegaplanClient $megaplanClient
     * @param string $uri
     * @param $data
     */
    public function __construct(MegaplanClient $megaplanClient, string $uri)
    {
        $this->megaplanClient = $megaplanClient;
        $this->uri = $uri;
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $requestParams = $this->getRequestParams();
        $data = $this->megaplanClient->get($this->uri, $requestParams);
        return $data;
    }

    /**
     * Prepares request parameters.
     *
     * @return array
     */
    abstract protected function getRequestParams();
}