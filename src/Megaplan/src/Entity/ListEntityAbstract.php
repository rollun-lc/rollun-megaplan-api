<?php

namespace rollun\api\megaplan\Entity;

abstract class ListEntityAbstract extends EntityAbstract
{
    /**
     * The Megaplan API allows send requests not more than this limit per hour
     */
    const MAX_REQUEST_COUNT_PRE_HOUR = 3000;

    /**
     * The Megaplan API allows to get rows count not more than this limit per request
     */
    const MAX_LIMIT = 100;

    /**
     * The Megaplan API has limit of requests per hour.
     * So when we get the list of the entities we have to check if we don't exceed this limit.
     * That's why we send requests in equal time intervals.
     *
     * @return float
     */
    protected function getRequestInterval()
    {
        return ceil(3600 / self::MAX_REQUEST_COUNT_PRE_HOUR * 1000);
    }
}