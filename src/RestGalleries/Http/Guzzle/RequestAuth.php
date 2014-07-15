<?php namespace RestGalleries\Http\Guzzle;

use CommerceGuys\Guzzle\Plugin\Oauth2\Oauth2Plugin;
use Guzzle\Plugin\Oauth\OauthPlugin;
use RestGalleries\Http\RequestAuth;

class GuzzleRequestAuth extends RequestAuth
{
    /**
     * Returns OAuth 1.0a protocol.
     *
     * @param  array       $credentials
     * @return OauthPlugin
     */
    protected function getOauth1Extension()
    {
        return new OauthPlugin($this->credentials);
    }

    /**
     * Retuns OAuth 2.0 protocol.
     *
     * @param  array        $credentials
     * @return Oauth2Plugin
     */
    protected function getOauth2Extension()
    {
        $oauth2 = new Oauth2Plugin();
        $oauth2->setAccessToken($this->credentials);

        return $oauth2;

    }
}
