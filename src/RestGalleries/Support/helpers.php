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
