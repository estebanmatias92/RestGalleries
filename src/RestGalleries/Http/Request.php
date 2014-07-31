<?php namespace RestGalleries\Http;

use RestGalleries\Http\RequestAdapter;

/**
 * Common http father to simplify client work with cache system selection and auth protocol selection, among others.
 */
abstract class Request implements RequestAdapter
{
    /**
     * [$body description]
     *
     * @var string
     */
    protected $body;

    /**
     * [$headers description]
     *
     * @var array
     */
    protected $headers;

    /**
     * [$query description]
     *
     * @var array
     */
    protected $query;

    /**
     * [$url description]
     *
     * @var string
     */
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

    abstract protected function newResponse($raw);

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


    public function __call($method, $parameters)
    {
        if ($this->isHttpVerb($method)) {
            $parameters[] = $method;

            return call_user_func_array([$this, 'sendRequest'], array_reverse($parameters));
        }

    }

    protected function isHttpVerb($verb)
    {
        $httpVerbs = ['GET', 'POST', 'PUT', 'DELETE'];

        if (! in_array($verb, $httpVerbs)) {
            return false;
        }

        return true;

    }

    abstract public function sendRequest($method = 'GET', $endPoint = '');

}
