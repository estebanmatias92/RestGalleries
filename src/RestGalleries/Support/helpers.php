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
        $arrayNonPrefixed = array_map($callback, $arrayFlipped);

        return array_flip($arrayNonPrefixed);

    }

}

if (! function_exists('array_unique_keys'))
{
    /**
     * Returns an array without repeated keys.
     *
     * @param  array  $array
     * @return array
     */
    function array_unique_keys(array $array)
    {
        $arrayFlipped  = array_flip($array);
        $arrayUnique   = array_unique($arrayFlipped);

        return array_flip($arrayUnique);

    }

}

if (! function_exists('get_class_namespace'))
{
    /**
     * Uses ReflectionClass to return the namespace from an absolute class name, or from an object.
     *
     * @param  string|object $class
     * @return string
     */
    function get_class_namespace($class)
    {
        $reflector = new \ReflectionClass($class);

        return $reflector->getNamespaceName();

    }

}

if (! function_exists('get_caller_function')) {

    function get_caller_function()
    {
        if (! isset(debug_backtrace()[2]['function'])) {
            return false;
        }

        return debug_backtrace()[2]['function'];

    }

}
