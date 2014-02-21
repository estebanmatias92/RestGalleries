<?php

namespace RestGalleries\Interfaces;

/**
 * Gallery description.
 */
interface Gallery
{
    public function get($args);
    public function find($args, $id);
}

