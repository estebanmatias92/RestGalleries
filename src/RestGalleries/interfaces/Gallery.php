<?php

namespace RestGalleries\interfaces;

/**
 * Gallery description.
 */
interface Gallery
{
    public function get($args);
    public function find($args, $id);
}

