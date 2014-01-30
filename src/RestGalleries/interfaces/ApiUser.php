<?php

namespace RestGalleries\interfaces;

/**
 * ApiUser description.
 */
interface ApiUser
{
    public function findByUsername($api_key, $secret_key, $username);
}

