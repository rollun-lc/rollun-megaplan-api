<?php


namespace rollun\api\megaplan\DataStore;


class Contractor extends AbstractMegaplanEntity
{
    /**
     *
     */
    const GET_ENTITY_URI = "/BumsCrmApiV01/Contractor/card.api";

    /**
     *
     */
    const GET_ENTITIES_URI = "/BumsCrmApiV01/Contractor/list.api";

    /**
     *
     */
    const CHANGE_ENTITY_URI = "/BumsCrmApiV01/Contractor/save.api";

    /**
     *
     */
    const GET_FIELDS_URI = "/BumsCrmApiV01/Contractor/listFields.api";
}