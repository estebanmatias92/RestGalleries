<?php namespace RestGalleries\Http\Guzzle;

use CommerceGuys\Guzzle\Plugin\Oauth2\Oauth2Plugin;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\FilesystemCache;
use Guzzle\Http\Client;
use Guzzle\Cache\DoctrineCacheAdapter;
use Guzzle\Plugin\Cache\CachePlugin;
use Guzzle\Plugin\Cache\DefaultCacheStorage;
use Guzzle\Plugin\Oauth\OauthPlugin;
use RestGalleries\Http\Http;
use RestGalleries\Http\ResponseAdapter;

/**
 * Specific http client based on Guzzle Client.
 */
class GuzzleHttp extends Http
{
    public function __construct()
    {
        $this->http = new Client;
    }

    public function setAuth(array $credentials)
    {
        parent::setAuth($credentials);

        $this->http->addSubscriber(
            $this->getAuth()
        );

        return $this;

    }

    /**
     * Returns OAuth 1.0a protocol.
     *
     * @param  array       $credentials
     * @return OauthPlugin
     */
    protected function getOauth1Extension()
    {
        return new OauthPlugin($this->authCredentials);
    }

    /**
     * Retuns OAuth 2.0 protocol.
     *
     * @param  array        $credentials
     * @return Oauth2Plugin
     */
    protected function getOauth2Extension()
    {
        $oauth2 = new Oauth2Plugin();
        $oauth2->setAccessToken($this->authCredentials);

        return $oauth2;

    }

    public function setCache($system = 'system', array $path = array())
    {
        parent::setCache($system, $path);

        $this->http->addSubscriber($this->getCache());

        return $this;

    }

    /**
     * Returns the Array cache system.
     *
     * @return ArrayCache
     */
    protected function getArraySystem()
    {
        return new CachePlugin([
            'adapter' => new DoctrineCacheAdapter(new ArrayCache())
        ]);

    }

    /**
     * Returns the file cache system.
     *
     * @param  array           $path
     * @return FilesystemCache
     */
    protected function getFileSystem()
    {
        return new CachePlugin([
            'storage' => new DefaultCacheStorage(
                new DoctrineCacheAdapter(
                    new FilesystemCache($this->cachePath['folder'])
                )
            )
        ]);

    }

    /**
     * Takes the http verb and endpoint (or uri/url) for the request and makes it.
     * Returns a raw response.
     *
     * @param  string $method
     * @param  string $endPoint
     * @return Object
     */
    protected function sendRequest($method = 'GET', $endPoint = '')
    {
        $method  = strtolower($method);
        $options = array_filter([
            'query'   => $this->getQuery(),
            'headers' => $this->getHeaders(),
            'body'    => $this->getBody()
        ]);


        $url     = $this->url . $endPoint;
        $request = call_user_func_array([$this->http, $method], [$url, array(), $options]);

        return $request->send();

    }

    /**
     * Takes the raw response and gives a simple and clean object.
     *
     * @param  object          $raw
     * @param  ResponseAdapter $response
     * @return Response
     */
    protected function processResponse($raw, ResponseAdapter $response)
    {
        $body       = $raw->getBody()->__toString();
        $headersRaw = $raw->getHeaders();
        $headers    = [];

        foreach ($headersRaw as $key => $value) {
            $headers[$key] = (string) $raw->getHeader($key);
        }

        $statusCode = $raw->getStatusCode();

        $this->response = $response
            ->setBody($body)
            ->setHeaders($headers)
            ->setStatusCode($statusCode);

    }

}
