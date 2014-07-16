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
        $this->auth = new OAuth;
    }

    protected function fetchTokenCredentials()
    {
        $auth = $this->auth;
        $auth = $auth::init($this->credentials);

        foreach ($this->endPoints as $method => $url) {
            $auth = call_user_func_array([$auth, $method], [$url]);
        }

        $auth->finally(function($data) use(&$tokenCredentials) {
            $tokenCredentials = $data;
        });

        return $tokenCredentials;

    }

}
