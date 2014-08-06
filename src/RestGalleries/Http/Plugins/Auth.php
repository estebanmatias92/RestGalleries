<?php namespace RestGalleries\Http\Plugins;

use RestGalleries\Auth\OhmyAuth\OhmyAuth;
use RestGalleries\Http\Plugins\RequestPluginAdapter;

/**
 * Normalizes the auth plugin of the Http client, receives input data to configure the plugin.
 */
abstract class Auth implements RequestPluginAdapter
{
    /**
     * Stores the authentication credentials as an array.
     *
     * @var array
     */
    protected $credentials;

    /**
     * Stores the auth protocol name.
     *
     * @var string
     */
    protected $protocol;

    /**
     * Initializes processing of plugin data.
     *
     * @param  array $credentials
     * @return void
     */
    public function __construct(array $credentials)
    {
        $this->processPluginData($credentials);
    }

    /**
     * Receives data to configure the plugin, then verifies and separates them into variables.
     *
     * @param  array $credentials
     * @throws \InvalidArgumentException
     * @return void
     */
    protected function processPluginData($credentials)
    {
        if (! $protocol = OhmyAuth::getAuthProtocol($credentials)) {
            throw new \InvalidArgumentException('Credential keys are invalid.');
        }

        $this->credentials = $credentials;
        $this->protocol    = $protocol;

    }

    /**
     * Calls another method that determine which plugin will be returned and return it.
     *
     * @return object
     */
    public function add()
    {
        return $this->getAuthExtension();
    }

    /**
     * Calls method that will return the auth extension that specified in the property $protocol.
     *
     * @return object
     */
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
