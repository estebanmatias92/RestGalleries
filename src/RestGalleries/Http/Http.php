<?php namespace RestGalleries\Http;

use RestGalleries\Auth\OhmyAuth\OhmyAuth;
use RestGalleries\Exception\HttpException;
use RestGalleries\Http\HttpAdapter;
use RestGalleries\Http\ResponseAdapter;
use RestGalleries\Http\Response;

/**
 * Common http father to simplify client work with cache system selection and auth protocol selection, among others.
 */
abstract class Http implements HttpAdapter
{
    protected $auth;
    protected $authCredentials;
    protected $authProtocol;
    protected $body;
    protected $cache;
    protected $cacheSystem;
    protected $cachePath;
    protected $http;
    protected $headers;
    protected $query;
    protected $response;
    protected $url;

    /**
     * Uses the construct to starts the class.
     *
     * @param  string $url
     * @return Object
     */
    public static function init($url = '')
    {
        $instance = new static;
        $instance->url = $url;

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
        if (! $protocol = OhmyAuth::getAuthProtocol($credentials)) {
            throw new \InvalidArgumentException('Credentials are invalid. ' . __METHOD__);
        }

        $this->authCredentials = $credentials;
        $this->authProtocol    = $protocol;
        $this->auth            = $this->getAuthExtension();

        return $this;

    }

    protected function getAuthExtension()
    {
        $method = 'get';
        $method .= $this->authProtocol;
        $method .= 'Extension';

        return call_user_func_array([$this, $method], [null]);

    }

    abstract protected function getOauth1Extension();
    abstract protected function getOauth2Extension();

    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * Takes a cache system name, selects it and stores it.
     *
     * @param  string $system
     * @param  array  $path
     * @throws InvalidArgumentException
     */
    public function setCache($system = 'system', array $path = array())
    {
        if (! $this->isValidCacheSystem($system)) {
            throw new \InvalidArgumentException('Cache system is invalid. ' . __METHOD__);
        }

        if (empty($path)) {
            $path = [
                'folder' => dirname(dirname(dirname(__FILE__))) . '/storage/cache',
            ];
        }

        $this->cacheSystem = $system;
        $this->cachePath   = $path;
        $this->cache       = $this->getCacheExtension();

        return $this;

    }

    protected function isValidCacheSystem($system)
    {
        $systems = ['array', 'system'];

        if (! in_array($system, $systems)) {
            return false;
        }

        return true;

    }

    protected function getCacheExtension()
    {
        $method = 'get';
        $method .= ucfirst($this->cacheSystem);
        $method .= 'System';

        return call_user_func_array([$this, $method], [null]);

    }

    abstract protected function getArraySystem();
    abstract protected function getFileSystem();

    public function getCache()
    {
        return $this->cache;
    }

    public function setQuery(array $query)
    {
        $this->query = $query;

        return $this;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Makes the request for GET http verb.
     *
     * @param string $endPoint
     */
    public function GET($endPoint = '')
    {
        return $this->makeHttpTransaction('GET', $endPoint);
    }

    public function POST($endPoint = '')
    {
        return $this->makeHttpTransaction('POST', $endPoint);
    }

    public function PUT($endPoint = '')
    {
        return $this->makeHttpTransaction('PUT', $endPoint);
    }

    public function DELETE($endPoint = '')
    {
        return $this->makeHttpTransaction('DELETE', $endPoint);
    }

    protected function makeHttpTransaction($method = 'GET', $endPoint = '')
    {
        $rawData = $this->sendRequest($method, $endPoint);
        $this->processResponse($rawData, new Response);

        return $this->getResponse();

    }

    abstract protected function sendRequest($method, $endPoint = '');
    abstract protected function processResponse($raw, ResponseAdapter $response);

    protected function getResponse()
    {
        return $this->response;
    }

}
