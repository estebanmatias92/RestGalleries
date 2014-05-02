<?php namespace RestGalleries\OAuth;

/**
 * OAuthClient description.
 */
interface OAuthClient
{
    public function connect(array $clientCredentials, array $endPoints);
    protected function getTokenCredentials();
    public function verifyCredentials(array $tokenCredentials, array $endPoint);
}
