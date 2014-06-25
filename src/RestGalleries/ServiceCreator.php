<?php namespace RestGalleries;

abstract class ServiceCreator
{
    protected $servicesNamespace = 'RestGalleries\\APIs\\';

    abstract public function fire($class);

    public static function make($class)
    {
        $instance = new static;

        return $instance->fire($class);

    }

    public static function makeGallery($service)
    {
        $instance = new static;
        $class    = $instance->servicesNamespace;
        $class    .= $service ;
        $class    .= '\\Gallery';

        return self::make($class);

    }

    public static function makeUser($service)
    {
        $instance = new static;
        $class    = $instance->servicesNamespace;
        $class    .= $service ;
        $class    .= '\\User';

        return self::make($class);

    }

}
