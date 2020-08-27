<?php


namespace rollun\api\megaplan\Exception;


class RequestLimitException extends \Exception
{
    protected $message = 'Request limit exceeded';
}