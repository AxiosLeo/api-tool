<?php

namespace api\tool\lib;

class Parse
{
    public static function xmlToArray($xml)
    {
        libxml_disable_entity_loader(true);

        return json_decode(
            \GuzzleHttp\json_encode(
                simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)
            ),
            true
        );
    }

    public static function jsonToArray($json)
    {
        $temp = json_decode($json, true);
        if (null === $temp && $json != $temp) {
            return $json;
        }

        return $temp;
    }
}
