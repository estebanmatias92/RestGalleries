<?php

namespace RestGalleries\Interfaces;

/**
 * Gallery description.
 */
interface Gallery
{
    public function get($api_key, $secret_key, $args);
    public function find($api_key, $secret_key, $args, $id);
}

