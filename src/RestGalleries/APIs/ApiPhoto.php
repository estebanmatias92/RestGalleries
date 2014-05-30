<?php namespace RestGalleries\APIs;

use RestGalleries\Http\HttpAdapter;

/**
 * ApiPhoto description.
 */
class ApiPhoto
{
    protected $endPoint;
    protected $http;

    public function __construct(HttpAdapter $http)
    {
        $this->http = $http::init($this->endPoint);
    }

    public function setAuth(array $tokenCredentials)
    {
        $this->http->setAuth($tokenCredentials);
    }

    public function setCache($fileSystem, array $path)
    {
        $this->http->setCache($fileSystem, $path);
    }

}
