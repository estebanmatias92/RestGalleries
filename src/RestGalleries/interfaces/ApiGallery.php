<?php

namespace RestGalleries\interfaces;

/**
 * ApiGallery description.
 */
interface ApiGallery
{
    public function get($api_key, $secret_key, $args);
    public function find($api_key, $secret_key, $args, $id);
}

