<?php namespace RestGalleries;

use RestGalleries\Interfaces\Api;
use RestGalleries\Exception\ApiNotFoundException;

class Factory extends ApiCreator
{
    public function fire($class)
    {
        if (!class_exists($class))
            throw new ApiNotFoundException('Api not found.');

        return $this->createApi(new $class);
    }

    public function createApi(Api $api)
    {
        return $api;
    }

}
