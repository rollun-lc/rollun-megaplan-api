<?php


namespace rollun\api\megaplan\DataStore;


class Deals extends AbstractMegaplanEntity
{
    /**
     *
     */
    const GET_ENTITY_URI = "/BumsTradeApiV01/Deal/card.api";

    /**
     *
     */
    const GET_ENTITIES_URI = "/BumsTradeApiV01/Deal/list.api";

    /**
     *
     */
    const CHANGE_ENTITY_URI = "/BumsTradeApiV01/Deal/save.api";

    /**
     *
     */
    const GET_FIELDS_URI = "/BumsTradeApiV01/Deal/listFields.api";
}