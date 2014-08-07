<?php namespace RestGalleries\Http;

use RestGalleries\Http\RequestAdapter;

/**
 * Takes care of Http requests, supports the use of multiple Http methods and plugins (oauth, cache).
 */
abstract class Request implements RequestAdapter
{
    /**
     * Stores the body of the Http request.
     *
     * @var string
     */
    protected $body;

    /**
     * Here will be stored the Http headers array.
     *
     * @var array
     */
    protected $headers;

    /**
     * Array of key-value to construct Http query for the request.
     *
     * @var array
     */
    protected $query;

    /**
     * Url for the request.
     *
     * @var string
     */
    protected $url;

    /**
     * Uses the construct to starts the class.
     *
     * @param  string $url
     * @return object
     */
    public static function init($url = '')
    {
        $instance = new static;
        $instance->url = $url;

        return $instance;

    }

    /**
     * It build the response object for an specific Http client.
     *
     * @param  object $raw
     * @return \RestGalleries\Http\ResponseAdapter
     */
    abstract protected function newResponse($raw);

    /**
     * Sets the keys-values of the query for the Http transaction.
     *
     * @param  array $query
     * @return \RestGalleries\Http\RequestAdapter
     */
    public function setQuery(array $query)
    {
        $this->query = $query;

        return $this;
    }

    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Sets string for the body of the Http request.
     *
     * @param  string $body
     * @return \RestGalleries\Http\RequestAdapter
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }

    /**
     * Sets the keys-values of the headers for the Http request.
     *
     * @param  array $headers
     * @return \RestGalleries\Http\RequestAdapter
     */
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
     * Allows call to the sendRequest method with summoning any of the Http verbs as object methods.
     * Takes the url introduced as 'endpoint' parameter.
     *
     * @param  string $method
     * @param  array  $parameters
     * @return \RestGalleries\Http\ResponseAdapter|null
     */
    public function __call($method, $parameters)
    {
        if ($this->isHttpVerb($method)) {
            $parameters[] = $method;

            return call_user_func_array([$this, 'sendRequest'], array_reverse($parameters));
        }

    }

    /**
     * Verifies if an string match some Http method.
     *
     * @param  string $verb
     * @return boolean
     */
    protected function isHttpVerb($verb)
    {
        $httpVerbs = ['GET', 'POST', 'PUT', 'DELETE'];

        if (! in_array($verb, $httpVerbs)) {
            return false;
        }

        return true;

    }

}
