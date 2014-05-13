<?php namespace RestGalleries\Interfaces;

/**
 * UserAdapter description.
 */
interface UserAdapter
{
    public function connect(array $clientCredentials);
    public function verifyCredentials(array $clientCredentials);

}
