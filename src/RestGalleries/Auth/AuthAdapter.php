<?php namespace RestGalleries\Auth;

use RestGalleries\Http\HttpAdapter;

/**
 * An interface to simplify the process of getting tokens. Has a method to check if the token credentials are still valid.
 */
interface AuthAdapter
{
    public static function connect(array $clientCredentials, array $endPoints, $checkUrl);
    public static function verifyCredentials(array $tokenCredentials, $checkUrl);
}
