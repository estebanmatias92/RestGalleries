<?php namespace RestGalleries\Http;

use RestGalleries\Exception\HttpException;
use RestGalleries\Http\HttpAdapter;
use RestGalleries\Http\Response;

/**
 * Common http father to simplify client work with cache system selection and auth protocol selection, among others.
 */
abstract class Http implements HttpAdapter
{
    protected $auth;
    protected $body;
    protected $cache;
    protected $client;
    protected $headers;
    protected $query;
    protected $url;

    abstract protected function getOAuth($credentials);
    abstract protected function getOAuth2($credentials);
    abstract protected function getCacheArray();
    abstract protected function getCacheFileSystem($path);

    /**
     * Uses the construct to starts the class.
     *
     * @param  string $url
     * @return Object
     */
    public static function init($url = '')
    {
        $instance = new static;

        if (is_string($url)) {
            $instance->url = $url;
        } else {
            throw new \InvalidArgumentException('Invalid argument type passed for parameter ($url), must be a string');
        }

        return $instance;

    }

    /**
     * Takes the credentials and selects what protocol should it use for the auth, and stores it (obviously).
     *
     * @param  array $credentials
     * @throws InvalidArgumentException
     */
    public function setAuth(array $credentials)
    {
        $OAuthProtocols  = $this->getOAuthProtocols();
        $credentialsKeys = array_keys($credentials);

        sort($credentialsKeys);

        foreach ($OAuthProtocols as $protocol => $array) {

            if (in_array($credentialsKeys, $array)) {

                $this->auth = call_user_func_array([$this, 'get'.$protocol], [$credentials]);

                return;

            }

        }

        throw new \InvalidArgumentException('Invalid argument value passed for parameter ($credentials)');


    }

    /**
     * Gives default credential keys for each protocol.
     *
     * @return array
     */
    protected function getOAuthProtocols()
    {
        return [
            'OAuth' => [
                ['consumer_key', 'consumer_secret'],
                ['consumer_key', 'consumer_secret', 'token', 'token_secret'],
            ],
            'OAuth2' => [
                ['access_token', 'expires'],
            ],
        ];

    }

    /**
     * Takes a cache system name, selects it and stores it.
     *
     * @param  string $system
     * @param  array  $path
     * @throws InvalidArgumentException
     */
    public function setCache($system = 'filesystem', array $path = array())
    {
        if (empty($path)) {
            $path = [
                'folder' => dirname(dirname(dirname(__FILE__))) . '/storage/cache',
            ];
        }

        $systems = ['array', 'filesystem'];

        if (in_array($system, $systems)) {

            $system      = ucfirst($system);
            $this->cache = call_user_func_array([$this, 'getCache'.$system], [$path]);

            return;

        }

        throw new \InvalidArgumentException('Invalid argument value passed for parameter ($system)');

    }

    public function setQuery(array $query)
    {
        $this->query = $query;
    }

    public function setBody($body)
    {
        if (is_string($body)) {
            $this->body = $body;
        } else {
            throw new \InvalidArgumentException('Invalid argument type passed for parameter ($body), must be a string');
        }

    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * Makes the request for GET http verb.
     *
     * @param string $endPoint
     */
    public function GET($endPoint = '')
    {
        $raw = $this->sendRequest('GET', $endPoint);

        return $this->getResponse($raw, new Response);

    }

    public function POST($endPoint = '')
    {
        $raw = $this->sendRequest('POST', $endPoint);

        return $this->getResponse($raw, new Response);
    }

    public function PUT($endPoint = '')
    {
        $raw = $this->sendRequest('PUT', $endPoint);

        return $this->getResponse($raw, new Response);
    }

    public function DELETE($endPoint = '')
    {
        $raw = $this->sendRequest('DELETE', $endPoint);

        return $this->getResponse($raw, new Response);
    }

}
