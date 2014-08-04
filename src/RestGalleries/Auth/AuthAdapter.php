<?php namespace RestGalleries\Auth;

/**
 * An interface to simplify the account authentication process and data obtaining.
 * It has methods to check if the tokens are still valid.
 * Also knows and recognizes automatically the auth protocols.
 */
interface AuthAdapter
{
    public static function connect(array $clientCredentials, array $endPoints, $checkUrl);
    public static function getAuthProtocol(array $credentials);
    public static function getOauth1Keys();
    public static function getOauth2Keys();
    public static function verifyCredentials(array $tokenCredentials, $checkUrl);

}
