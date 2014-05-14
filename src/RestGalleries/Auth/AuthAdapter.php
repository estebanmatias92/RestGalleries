<?php namespace RestGalleries\Auth;

/**
 * An interface to simplify the account authentication process and data obtaining. It has a metedo to check the token are still valid.
 */
interface AuthAdapter
{
    public static function connect(array $clientCredentials, array $endPoints, $checkUrl);
    public static function verifyCredentials(array $tokenCredentials, $checkUrl);
}
