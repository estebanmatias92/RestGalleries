<?php namespace RestGalleries;

use RestGalleries\Exception\ApiNotFoundException;


class Factory extends ServiceCreator
{
    public function fire($class)
    {
        if (! class_exists($class))
        {
            throw new ApiNotFoundException('Api not found.');
        }

        return new $class;

    }

}
