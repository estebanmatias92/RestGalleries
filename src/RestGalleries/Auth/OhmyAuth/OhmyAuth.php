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
        parent::__construct();

        $this->client = new OAuth;
    }

    protected function getTokenCredentials()
    {
        $client = $this->client;
        $client = $client::init($this->clientCredentials);

        foreach ($this->endPoints as $method => $url) {
            $client = call_user_func_array([$client, $method], [$url]);
        }

        $client->finally(function($data) use(&$tokenCredentials) {
            $tokenCredentials = $data;
        });

        return $tokenCredentials;

    }

}
