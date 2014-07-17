<?php namespace RestGalleries\Http\Plugins;

use RestGalleries\Auth\OhmyAuth\OhmyAuth;
use RestGalleries\Http\Plugins\RequestPluginAdapter;

abstract class Auth implements RequestPluginAdapter
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

    public function __construct(array $credentials)
    {
        $this->processPluginData($credentials);
    }

    /**
     * [processPluginData description]
     *
     * @param  [type] $credentials
     * @return [type]
     */
    protected function processPluginData($credentials)
    {
        if (! $protocol = OhmyAuth::getAuthProtocol($credentials)) {
            throw new \InvalidArgumentException('Credentials are invalid.');
        }

        $this->credentials = $credentials;
        $this->protocol    = $protocol;
    }

    /**
     * Takes the credentials and selects what protocol should it use for the auth, and stores it (obviously).
     *
     * @param  array $credentials
     * @throws InvalidArgumentException
     */
    public function add()
    {
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
