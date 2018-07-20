<?php


namespace rollun\api\megaplan\Entity\Deal\Factory;
use rollun\api\megaplan\Entity\Factory\AbstractFactory;


class AbstractDealFactory extends AbstractFactory
{
    const DEALS_KEY = 'deals';
    const LIST_FIELDS_KEY = 'listFields';
    const FILTER_FIELD_KEY = 'filterField';
    const FILTER_FIELD_PROGRAM_KEY = 'Program';
    const REQUESTED_FIELDS_KEY = 'requestedFields';
    const EXTRA_FIELDS_KEY = 'extraFields';
}