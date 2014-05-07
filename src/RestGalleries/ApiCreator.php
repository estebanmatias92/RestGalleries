<?php namespace RestGalleries;

use RestGalleries\Interfaces\Api;

abstract class ApiCreator
{
    abstract public function fire($class);
    abstract public function createApi(Api $api);

    public static function make($api)
    {
        if (!is_string($api))
        {
            throw new \InvalidArgumentException('Invalid argument type.');
        }

        $instance = new static;

        return $instance->fire($api);

    }
}