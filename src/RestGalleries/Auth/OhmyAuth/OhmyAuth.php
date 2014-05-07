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
     * Takes credentials and URIs and calls to the client to process and get the token credentials.
     *
     * @param  array               $clientCredentials
     * @param  array               $endPoints
     * @return object/string/array
     */
    public static function connect(array $clientCredentials, array $endPoints)
    {
        $clientCredentials = array_change_key_case($clientCredentials);
        $endPoints         = array_change_key_case($endPoints);

        $instance         = new static;
        $client           = $instance->client;
        $instance->client = $client::init($clientCredentials);

        foreach ($endPoints as $method => $url) {
            $instance->client = call_user_func_array(array($instance->client, $method), array($url));
        }

        return $instance->getTokenCredentials();

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
     * @param  string          $uri
     * @return json/xml/string
     */
    public static function verifyCredentials(array $tokenCredentials, $uri)
    {
        $instance       = new static;
        $http           = $instance->http;
        $instance->http = $http::init($uri);

        $instance->http->setAuth($tokenCredentials);

        $response = $instance->http->GET();

        return $response->getBody();

    }

}
