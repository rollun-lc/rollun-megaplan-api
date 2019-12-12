<?php


namespace rollun\api\megaplan\DataStore;


use rollun\datastore\DataStore\DataStoreException;

/**
 * Class Offer
 * @package rollun\api\megaplan\DataStore
 * @see https://dev.megaplan.ru/api/API_invoices.html#id20
 */
class Offer extends AbstractMegaplanEntity
{
    /**
     *
     */
    public const GET_ENTITIES_URI = '/BumsTradeApiV01/Offer/list.api';

    /**
     *
     */
    public const CHANGE_ENTITY_URI = '/BumsTradeApiV01/Offer/save.api';

    /**
     *
     */
    public const GET_FIELDS_URI = '/BumsTradeApiV01/Offer/listFields.api';

    public function read($id)
    {
        throw new DataStoreException('Read method not support for Offer store.');
    }
}