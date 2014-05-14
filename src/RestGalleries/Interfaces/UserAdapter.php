<?php namespace RestGalleries\Interfaces;

use RestGalleries\Auth\AuthAdapter;

/**
 * UserAdapter description.
 */
interface UserAdapter
{
    public function __construct(AuthAdapter $auth);
    public function connect(array $clientCredentials);
    public function verifyCredentials(array $clientCredentials);

}
