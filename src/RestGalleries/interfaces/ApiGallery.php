<?php

namespace RestGalleries\interfaces;

/**
 * ApiGallery description.
 */
interface ApiGallery
{
    public function get($args);
    public function find($args, $id);
}

