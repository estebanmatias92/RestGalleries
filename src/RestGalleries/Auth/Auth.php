<?php namespace RestGalleries\Auth;

use RestGalleries\Http\HttpAdapter;

/**
 * Common Auth father that stores the properties for all auth clients.
 */
abstract class Auth implements AuthAdapter
{
    protected $client;
    protected $http;

    /**
     * Takes credentials and URIs and calls to the client to process and get the token credentials.
     *
     * @param  array               $clientCredentials
     * @param  array               $endPoints
     * @return object/string/array
     */
    public static function connect(array $clientCredentials, array $endPoints, $checkUrl)
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

    abstract protected function getTokenCredentials();

}
