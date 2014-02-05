<?php

namespace RestGalleries\Interfaces;

/**
 * User description.
 */
interface User
{
    public function findByUsername($api_key, $secret_key, $username);
}

