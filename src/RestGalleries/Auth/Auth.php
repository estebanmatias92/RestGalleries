<?php namespace RestGalleries\Auth;

use RestGalleries\Http\HttpAdapter;

/**
 * Common Auth father that stores the properties for all auth clients.
 */
abstract class Auth implements AuthAdapter
{
    protected $client;
    protected $http;

    abstract protected function getTokenCredentials();

}
