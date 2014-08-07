<?php namespace RestGalleries\Http\Guzzle\Plugins;

use CommerceGuys\Guzzle\Plugin\Oauth2\Oauth2Plugin;
use Guzzle\Plugin\Oauth\OauthPlugin;
use RestGalleries\Http\Plugins\Auth;

/**
 * Adapter class for auth plugins from the Http client 'Guzzle'.
 */
class GuzzleAuth extends Auth
{
    /**
     * Returns authentication plugin for the protocol 'oauth1'.
     *
     * @return \Guzzle\Plugin\Oauth\OauthPlugin
     */
    protected function getOauth1Extension()
    {
        return new OauthPlugin($this->credentials);
    }

    /**
     * Returns authentication plugin for the protocol 'oauth2'.
     *
     * @return \CommerceGuys\Guzzle\Plugin\Oauth2\Oauth2Plugin
     */
    protected function getOauth2Extension()
    {
        $oauth2 = new Oauth2Plugin();
        $oauth2->setAccessToken($this->credentials);

        return $oauth2;

    }

}
