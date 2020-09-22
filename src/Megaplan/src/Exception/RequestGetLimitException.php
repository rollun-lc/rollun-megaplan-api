<?php


namespace rollun\api\megaplan\Exception;


class RequestGetLimitException extends RequestLimitException
{
    protected $message = 'Get request limit exceeded';
}