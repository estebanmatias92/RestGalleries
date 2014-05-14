<?php namespace RestGalleries\Auth;

use RestGalleries\Auth\AuthAdapter;
use RestGalleries\Exception\AuthException;
use RestGalleries\Http\Guzzle\GuzzleHttp;

/**
 * Common Auth father that stores the properties for all auth clients.
 */
abstract class Auth implements AuthAdapter
{
    protected $client;
    protected $http;

    protected $endPoints;
    protected $clientCredentials;
    protected $protocol;

    public function __construct()
    {
        $this->http   = new GuzzleHttp;
    }

    /**
     * Takes credentials and URIs, checks credentials and gets tokens.
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
     * [filterTokens description]
     *
     * @param  array $tokenCredentials
     * @return array
     */
    protected function filterTokens($tokenCredentials)
    {
        if ($this->protocol == 1) {
            foreach ($tokenCredentials as $key => $value) {
                $key = str_replace('oauth_', '', $key);
            }
        }

        $credentials = array_merge($this->clientCredentials, $tokenCredentials);
        $keys        = call_user_func_array([$this, 'getAuth'.$this->protocol.'DefaultKeys'], [null]);

        return array_only($credentials, $keys['token_credentials']);
    }

    /**
     * [getAccount description]
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
     * [addDataTokens description]
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
     * [verifyCredentials description]
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
     * [checkProtocol description]
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
            throw new AuthException('Invalid client credentials');
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
     * [getAuth1DefaultKeys description]
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
     * [getAuth2DefaultKeys description]
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
