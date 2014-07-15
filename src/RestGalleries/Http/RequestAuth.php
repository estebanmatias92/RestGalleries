<?php namespace RestGalleries\Http;

use RestGalleries\Auth\OhmyAuth\OhmyAuth;

abstract class RequestAuth
{
    /**
     * [$credentials description]
     *
     * @var array
     */
    protected $credentials;

    /**
     * [$protocol description]
     *
     * @var string
     */
    protected $protocol;

    /**
     * Takes the credentials and selects what protocol should it use for the auth, and stores it (obviously).
     *
     * @param  array $credentials
     * @throws InvalidArgumentException
     */
    public static function addAuth(array $credentials)
    {
        if (! $protocol = OhmyAuth::getAuthProtocol($credentials)) {
            throw new \InvalidArgumentException('Credentials are invalid. ' . __METHOD__);
        }

        $this->credentials = $credentials;
        $this->protocol    = $protocol;

        return $this->getAuthExtension();

    }

    protected function getAuthExtension()
    {
        $method = 'get';
        $method .= $this->protocol;
        $method .= 'Extension';

        return call_user_func_array([$this, $method], [null]);

    }

    abstract protected function getOauth1Extension();
    abstract protected function getOauth2Extension();

}
