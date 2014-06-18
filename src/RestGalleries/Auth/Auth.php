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
     * Stores the Auth client from child class.
     *
     * @var object
     */
    protected $client;

    /**
     * Stores the Http client.
     *
     * @var object
     */
    protected $http;

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

    public function __construct()
    {
        $this->http = new GuzzleHttp;
    }

    /**
     * Takes credentials and URLs, checks credentials and gets tokens.
     * Returns an account with their credentials.
     *
     * @param  array  $clientCredentials
     * @param  array  $endPoints
     * @param  string $checkUrl
     * @return object
     */
    public static function connect(array $clientCredentials, array $endPoints, $checkUrl)
    {
        $instance = new static;

        if (! $instance->protocol = self::getAuthProtocol($clientCredentials)) {
            throw new AuthException('Credentials keys are invalid');
        }

        $instance->credentials = $clientCredentials;
        $instance->endPoints   = $endPoints;
        $tokenCredentials      = $instance->getTokenCredentials();

        $instance->addToCredentials($tokenCredentials);
        $instance->filterCredentials('token_credentials');

        return $instance->getAccountData($checkUrl);

    }

    /**
     * Calls to the auth client and "makes the magic" returning token credentials.
     *
     * @return array
     */
    abstract protected function getTokenCredentials();

    /**
     * [addToCredentials description]
     *
     * @param  array $credentials
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
     * Filters the given tokens for use them with the "Auth::getAccountData" method.
     *
     * @param  array $tokenCredentials
     * @return void
     */
    protected function filterCredentials($filter = 'client_credentials')
    {
        if (! in_array($filter, ['client_crecentials', 'token_credentials'])) {
            return;
        }

        $this->removeCredentialPrefixes('oauth_');

        $protocol = ucfirst($this->protocol);
        $keys     = call_user_func_array([$this, 'get' . $protocol . 'Keys'], [null]);

        $this->credentials = array_only(
            $this->credentials,
            $keys[$filter]
        );

    }

    /**
     * [removeCredentialPrefixes description]
     *
     * @param  string $prefix
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
     * @param  array  $tokenCredentials
     * @param  string $checkUrl
     * @return object
     */
    protected function getAccountData($checkUrl)
    {
        $http           = $this->http;
        $http           = $http::init($checkUrl);
        $http->setAuth($this->credentials);

        $response       = $http->GET();
        $responseObject = $response->getBody();
        $this->addDataTokens($responseObject, $this->credentials);

        return $responseObject;

    }

    /**
     * Add token credentials to the object.
     *
     * @param object $object
     * @param array $tokens
     */
    protected function addDataTokens(&$object, $tokenCredentials)
    {
        $tokens = &$object->tokens;

        $callback = function ($value, $key) use ($tokens) {
            $tokens = array_add($tokens, $key, $value);
        };

        return array_walk($tokenCredentials, $callback);

    }

    /**
     * Checks the token credentials and returns an object with its account data.
     *
     * @param  array  $tokenCredentials
     * @param  string $checkUrl
     * @return object
     */
    public static function verifyCredentials(array $tokenCredentials, $checkUrl)
    {
        $instance = new static;

        if (! $instance->protocol = self::getAuthProtocol($tokenCredentials)) {
            throw new AuthException('Credentials keys are invalid');
        }

        $instance->addToCredentials($tokenCredentials);
        $instance->filterCredentials('token_credentials');

        return $instance->getAccountData($checkUrl);

    }

    public static function getAuthProtocol(array $credentials)
    {
        $instance       = new static;
        $credentialKeys = array_keys($credentials);

        if ($instance->isOauth1($credentialKeys)) {
            return 'oauth1';
        }

        if ($instance->isOauth2($credentialKeys)) {
            return 'oauth2';
        }

        return false;
        throw new AuthException('Credentials keys are invalid');

    }

    protected function isOauth1($credentials)
    {

        if (in_array($credentials, $this->getOauth1Keys())) {
            return true;
        }

        return false;

    }

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
