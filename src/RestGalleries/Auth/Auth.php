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
     * Input credentials to start the connect process.
     *
     * @var array
     */
    protected $clientCredentials;

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

        $instance->clientCredentials = array_change_key_case($clientCredentials);
        $instance->endPoints         = array_change_key_case($endPoints);

        $instance->checkProtocol($instance->clientCredentials);

        $tokenCredentials = $instance->getTokenCredentials();
        $tokenCredentials = $instance->filterTokens($tokenCredentials);

        return $instance->getAccount($tokenCredentials, $checkUrl);

    }

    /**
     * Calls to the auth client and "makes the magic" returning token credentials.
     *
     * @return array
     */
    abstract protected function getTokenCredentials();

    /**
     * Filters the given tokens for use them with the "Auth::getAccount" method.
     *
     * @param  array $tokenCredentials
     * @return array
     */
    protected function filterTokens($tokenCredentials)
    {
        if ($this->protocol == 1) {
            foreach ($tokenCredentials as $key => $value) {
                unset($tokenCredentials[$key]);
                $key = str_replace('oauth_', '', $key);
                $tokenCredentials[$key] = $value;
            }
        }

        $credentials = array_merge($this->clientCredentials, $tokenCredentials);
        $keys        = call_user_func_array([$this, 'getAuth'.$this->protocol.'DefaultKeys'], [null]);

        return array_only($credentials, $keys['token_credentials']);
    }

    /**
     * Makes the http request to the "checkUrl", and gets an object with account data.
     * Additionally it adds token credentials data to the object by if needed.
     *
     * @param  array  $tokenCredentials
     * @param  string $checkUrl
     * @return object
     */
    protected function getAccount($tokenCredentials, $checkUrl)
    {
        $http           = $this->http;
        $http           = $http::init($checkUrl);
        $http->setAuth($tokenCredentials);

        $response       = $http->GET();
        $responseObject = $response->getBody();
        $this->addDataTokens($responseObject, $tokenCredentials);

        return $responseObject;

    }

    /**
     * Add token credentials to the object.
     *
     * @param object $object
     * @param array $tokens
     */
    protected function addDataTokens(&$object, $tokens)
    {
        foreach ($tokens as $key => $value) {

            $key = str_replace('oauth_', '', $key);

            if (!isset($object->tokens[$key])) {
                $object->tokens[$key] = $value;
            }

        }

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
        $instance->checkProtocol($tokenCredentials);

        return $instance->getAccount($tokenCredentials, $checkUrl);

    }

    /**
     * Checks credentials and sets up the OAuth protocol number, or throw an exception on wrong case.
     *
     * @param  array $credentials
     * @throws RestGalleries\Exception\AuthException
     */
    protected function checkProtocol($credentials)
    {
        $keys = array_keys($credentials);

        if ($this->isAuth1($keys)) {
            $this->protocol = 1;
        } elseif ($this->isAuth2($keys)) {
            $this->protocol = 2;
        } else {
            throw new AuthException('Credentials keys are invalid');
        }

    }

    protected function isAuth1($credentials)
    {

        if (in_array($credentials, $this->getAuth1DefaultKeys())) {
            return true;
        }

        return false;

    }

    protected function isAuth2($credentials)
    {
        if (in_array($credentials, $this->getAuth2DefaultKeys())) {
            return true;
        }

        return false;

    }

    /**
     * Credential keys (token-client ouath1) necessary for normalize the obtained tokens, and check the client token for know the protocol.
     *
     * @return array
     */
    protected function getAuth1DefaultKeys()
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
    protected function getAuth2DefaultKeys()
    {
        return [
            'client_credentials' => ['client_id', 'client_secret', 'redirect'],
            'token_credentials' => ['acces_token', 'expires']
        ];
    }

}
