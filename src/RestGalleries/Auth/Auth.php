<?php namespace RestGalleries\Auth;

use RestGalleries\Auth\AuthAdapter;
use RestGalleries\Exception\AuthException;
use RestGalleries\Http\Guzzle\GuzzleHttp;

/**
 * Common father class, makes the hard work to get the account data and verify the tokens.
 */
abstract class Auth implements AuthAdapter
{
    /**
     * "OAuth dance" URLs for the connect process.
     *
     * @var array
     */
    protected $endPoints;

    /**
     * Stores the client credentials and token credentials.
     *
     * @var array
     */
    protected $credentials = [];

    /**
     * What protocol is going to treat?.
     *
     * @var integer
     */
    protected $protocol;

    /**
     * Takes credentials and URLs, checks credentials and gets tokens.
     * Returns an account with their credentials.
     *
     * @param  array  $clientCredentials
     * @param  array  $endPoints
     * @param  string $userDataUrl
     * @return object
     */
    public static function connect(array $clientCredentials, array $endPoints, $userDataUrl)
    {
        $instance = new static;

        if (! $protocol = self::getAuthProtocol($clientCredentials)) {
            throw new AuthException('Credentials keys are invalid.');
        }

        $instance->protocol    = $protocol;
        $instance->credentials = $clientCredentials;
        $instance->endPoints   = $endPoints;
        $tokenCredentials      = $instance->fetchTokenCredentials();

        $instance->addToCredentials($tokenCredentials);
        $instance->filterCredentialsByKey('token_credentials');

        $userData = $instance->fetchUserData($userDataUrl);

        return $userData;

    }

    /**
     * Calls to the auth client and "makes the magic" returning token credentials.
     *
     * @return array
     */
    abstract protected function fetchTokenCredentials();

    /**
     * Merge the new credential values to the existing credentials.
     *
     * @param  array  $credentials
     * @return void
     */
    protected function addToCredentials(array $credentials)
    {
        $this->credentials = array_merge(
            $this->credentials,
            $credentials
        );
    }

    /**
     * Filters the given credentials for use them with the "Auth::fetchUserData" method.
     *
     * @param  array  $tokenCredentials
     * @return void
     */
    protected function filterCredentialsByKey($filter = 'client_credentials')
    {
        $this->removeCredentialPrefixes('oauth_');

        $method = 'get';
        $method .= ucfirst($this->protocol);
        $method .= 'Keys';

        $keys = call_user_func_array([$this, $method], [null]);

        $this->credentials = array_only(
            $this->credentials,
            $keys[$filter]
        );

    }

    /**
     * Remove an string from the the beginning of each credential key.
     *
     * @param  string  $prefix
     * @return void
     */
    protected function removeCredentialPrefixes($prefix)
    {
        $this->credentials = array_remove_key_prefix(
            $this->credentials,
            $prefix
        );
    }

    /**
     * Makes the http request to the "checkUrl", and gets an object with account data.
     * Additionally it adds token credentials data to the object by if needed.
     *
     * @param  string $userDataUrl
     * @return object
     */
    protected function fetchUserData($userDataUrl)
    {
        $request = $this->newRequest();
        $plugins = [
            'auth' => $this->newRequesAuth(),
        ];

        $userData = $request::init($userDataUrl)
            ->addPlugins($plugins)
            ->GET()
            ->getBody();

        $this->addDataTokens($userData, $this->credentials);

        return $userData;

    }

    /**
     * [newRequestAuth description]
     *
     * @param  [type] $requestAuth
     * @return [type]
     */
    public function newRequestAuth(RequestAuth $requestAuth = null)
    {
        if (empty($requestAuth)) {
            $requestAuth = new GuzzleRequestAuth;
        }

        return $requestAuth::add($this->credentials);

    }

    /**
     * [newRequest description]
     *
     * @param  [type] $request
     * @return [type]
     */
    public function newRequest(Request $request = null)
    {
        if (empty($request)) {
            $request = new GuzzleRequest;
        }

        return $request;

    }

    /**
     * Add token credentials to the object.
     *
     * @param  object  $object
     * @param  array   $tokens
     * @return void
     */
    protected function addDataTokens(&$object, $tokenCredentials)
    {
        $tokens = &$object->tokens;

        $callback = function ($value, $key) use ($tokens) {
            $tokens = array_add($tokens, $key, $value);
        };

        array_walk($tokenCredentials, $callback);

    }

    /**
     * Checks the token credentials and returns an object with its account data.
     *
     * @param  array  $tokenCredentials
     * @param  string $userDataUrl
     * @return object
     */
    public static function verifyCredentials(array $tokenCredentials, $userDataUrl)
    {
        $instance = new static;

        if (! $protocol = self::getAuthProtocol($tokenCredentials)) {
            throw new AuthException('Credentials keys are invalid.');
        }

        $instance->protocol = $protocol;

        $instance->addToCredentials($tokenCredentials);
        $instance->filterCredentialsByKey('token_credentials');

        $userData = $instance->fetchUserData($userDataUrl);

        return $userData;

    }

    /**
     * Checks the credentials and returns the name of the auth system used, if credentials are not founds, returns false.
     *
     * @param  array  $credentials
     * @return string|boolean
     */
    public static function getAuthProtocol(array $credentials)
    {
        $instance       = new static;
        $credentialKeys = array_keys($credentials);
        sort($credentialKeys);

        if ($instance->isOauth1($credentialKeys)) {
            return 'oauth1';
        }

        if ($instance->isOauth2($credentialKeys)) {
            return 'oauth2';
        }

        return false;

    }

    /**
     * Checks if the credentials are using OAuth1.0.
     *
     * @param  array  $credentials
     * @return boolean
     */
    protected function isOauth1($credentials)
    {
        if (in_array($credentials, $this->getOauth1Keys())) {
            return true;
        }

        return false;

    }

    /**
     * Checks if the credentials are using OAuth2.0.
     *
     * @param  array  $credentials
     * @return boolean
     */
    protected function isOauth2($credentials)
    {
        if (in_array($credentials, $this->getOauth2Keys())) {
            return true;
        }

        return false;

    }

    /**
     * Credential keys (token-client ouath1) necessary for normalize the obtained tokens, and check the client token for know the protocol.
     *
     * @return array
     */
    public static function getOauth1Keys()
    {
        return [
            'client_credentials' => ['consumer_key', 'consumer_secret', 'callback'],
            'token_credentials' => ['consumer_key', 'consumer_secret', 'token', 'token_secret']
        ];
    }

    /**
     * Credential keys (token-client ouath2)necessary for normalize the obtained tokens, and check for know the protocol.
     *
     * @return array
     */
    public static function getOauth2Keys()
    {
        return [
            'client_credentials' => ['client_id', 'client_secret', 'redirect'],
            'token_credentials' => ['acces_token', 'expires']
        ];
    }

}
