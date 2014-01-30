<?php

namespace RestGalleries\interfaces;

/**
 * ApiUser description.
 */
interface ApiUser
{
    public function findByUsername($args, $username);
}

