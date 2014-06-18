<?php namespace RestGalleries\Auth\OhmyAuth;

use RestGalleries\Auth\Auth;
use ohmy\Auth as OAuth;

/**
 * Specific auth client made on Ohmy-Auth Client.
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
        $client = $client::init($this->credentials);

        foreach ($this->endPoints as $method => $url) {
            $client = call_user_func_array([$client, $method], [$url]);
        }

        $client->finally(function($data) use(&$tokenCredentials) {
            $tokenCredentials = $data;
        });

        return $tokenCredentials;

    }

}
