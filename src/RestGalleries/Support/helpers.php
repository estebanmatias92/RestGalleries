<?php

if (!function_exists('is_json'))
{
    /**
     * Check if the string is JSON.
     *
     * @param  string  $string
     * @return boolean
     */
    function is_json($string)
    {
        json_decode($string);

        return (json_last_error() == JSON_ERROR_NONE);
    }

}

if (!function_exists('is_xml'))
{
    /**
     * Check if the string is XML.
     *
     * @param  string  $string
     * @return boolean
     */
    function is_xml($string)
    {
        libxml_use_internal_errors(true);

        $result = simplexml_load_string($string);

        return (!$result) ? false : true;
    }

}

if (!function_exists('xml_decode'))
{
    /**
     * Parse xml string to PHP array or object.
     *
     * @param  string       $string
     * @param  boolean      $array
     * @return array/object
     */
    function xml_decode($string, $array = false)
    {
        if ($array == false) {
            return simplexml_load_string($string);
        } else {
            $xml  = simplexml_load_string($string);
            $json = json_encode($xml);

            return json_decode($json, true);
        }

    }
}


if (! function_exists('array_remove_key_prefix')) {

    /**
     * Remove the given string from all key prefixes.
     *
     * @param  array  $array
     * @param  string $prefix
     * @return array
     */
    function array_remove_key_prefix(array $array, $prefix)
    {
        if ($type = gettype($prefix) != 'string') {
            throw new \InvalidArgumentException('Prefix argument should be an string, ' . $type . ' given.');
        }

        $stringLength = strlen($prefix);

        $callback = function ($item) use ($prefix, $stringLength) {
            if (strpos($item, $prefix) !== 0) {
                return $item;
            }

            return substr($item, $stringLength);

        };

        $arrayFlipped    = array_flip($array);
        $arrayUnPrefixed = array_map($callback, $arrayFlipped);

        return array_flip($arrayUnPrefixed);

    }

}
