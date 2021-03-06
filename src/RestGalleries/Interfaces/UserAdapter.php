<?php namespace RestGalleries\Interfaces;

use RestGalleries\Auth\AuthAdapter;

/**
 * Simplifies the User class work and implements the AuthAdapter for make the hard work with the oauth connection and token verification.
 */
interface UserAdapter
{
    public function connect(array $clientCredentials);
    public function verifyCredentials(array $clientCredentials);
    public function newAuth(AuthAdapter $auth =null);

}
