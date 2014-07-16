<?php namespace RestGalleries\Http\Plugins;

use RestGalleries\Auth\OhmyAuth\OhmyAuth;

abstract class Auth
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
    public static function add(array $credentials)
    {
        if (! $protocol = OhmyAuth::getAuthProtocol($credentials)) {
            throw new \InvalidArgumentException('Credentials are invalid.');
        }

        $instance              = new static;
        $instance->credentials = $credentials;
        $instance->protocol    = $protocol;

        return $instance->getAuthExtension();

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
