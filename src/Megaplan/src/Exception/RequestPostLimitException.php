<?php


namespace rollun\api\megaplan\Exception;


class RequestPostLimitException extends RequestLimitException
{
    protected $message = 'Post request limit exceeded';
}