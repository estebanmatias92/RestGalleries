<?php namespace RestGalleries\Auth\OhmyAuth;

use RestGalleries\Exception\AuthException;
use RestGalleries\Auth\Auth;
use RestGalleries\Http\Guzzle\GuzzleHttp;
use ohmy\Auth as OAuth;

/**
 * Specific auth client based on Ohmy-Auth Client.
 */
class OhmyAuth extends Auth
{
    public function __construct()
    {
        $this->http   = new GuzzleHttp;
        $this->client = new OAuth;
    }

    /**
     * Gets token data.
     *
     * @return string
     */
    protected function getTokenCredentials()
    {
        $this->client->finally(function($data) use(&$tokenCredentials) {
            $tokenCredentials = $data;
        });

        return $tokenCredentials;

    }

    /**
     * Makes a request to test the token credentials, and returns response body.
     *
     * @param  array           $tokenCredentials
     * @param  string          $checkUrl
     * @return json/xml/string
     */
    public static function verifyCredentials(array $tokenCredentials, $checkUrl)
    {
        $instance       = new static;
        $http           = $instance->http;
        $instance->http = $http::init($checkUrl);

        $instance->http->setAuth($tokenCredentials);

        $response = $instance->http->GET();

        return $response->getBody();

    }

}
