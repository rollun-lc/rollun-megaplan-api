<?php


namespace rollun\api\megaplan\Traits;


/**
 * Trait CustomFieldMappingTrait
 * @package rollun\api\megaplan\Command
 */
trait CustomFieldMappingTrait
{
    /**
     * @param array|object $item
     * @return array
     */
    public function customFieldMap($item) {
        $unwarpItem = [];
        foreach ($item as $key => $value) {
            if (preg_match('/Category([\d]+)CustomField(?<field_name>[\w\d]+)$/', $key, $match)) {
                $key = $match["field_name"];
            }
            $unwarpItem[$key] = $value;
        }
        return $unwarpItem;
    }
}