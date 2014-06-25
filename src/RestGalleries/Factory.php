<?php namespace RestGalleries;

use RestGalleries\Exception\ApiNotFoundException;


class Factory extends ApiCreator
{
    public function fire($class)
    {
        $class_namespace = 'RestGalleries\\APIs\\' . $class;

        if (! class_exists($class_namespace))
        {
            throw new ApiNotFoundException('Api not found.');
        }

        return $this->createApi(new $class_namespace);

    }

    public function createApi($api)
    {
        return $api;
    }

}
