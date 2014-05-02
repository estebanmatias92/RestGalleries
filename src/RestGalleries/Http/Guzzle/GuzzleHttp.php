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
        $this->client = new Client;
    }

    public function setAuth(array $credentials)
    {
        parent::setAuth($credentials);

        $this->client->addSubscriber($this->auth);

    }

    /**
     * Returns OAuth 1.0a protocol.
     *
     * @param  array       $credentials
     * @return OauthPlugin
     */
    protected function getOAuth($credentials)
    {
        $oauth = new OauthPlugin($credentials);

        return $oauth;

    }

    /**
     * Retuns OAuth 2.0 protocol.
     *
     * @param  array        $credentials
     * @return Oauth2Plugin
     */
    protected function getOAuth2($credentials)
    {
        $oauth2 = new Oauth2Plugin();
        $oauth2->setAccessToken($credentials);

        return $oauth2;

    }

    public function setCache($system = 'filesystem', array $path = array())
    {
        parent::setCache($system, $path);

        $this->client->addSubscriber($this->cache);

    }

    /**
     * Returns the Array cache system.
     *
     * @return ArrayCache
     */
    protected function getCacheArray()
    {
        return new CachePlugin(array(
            'adapter' => new DoctrineCacheAdapter(new ArrayCache())
        ));

    }

    /**
     * Returns the file cache system.
     *
     * @param  array           $path
     * @return FilesystemCache
     */
    protected function getCacheFileSystem($path)
    {
        return new CachePlugin(array(
            'storage' => new DefaultCacheStorage(
                new DoctrineCacheAdapter(
                    new FilesystemCache($this->path['folder'])
                )
            )
        ));

    }

    /**
     * Takes the http verb and endpoint (or uri/url) for the request and makes it.
     * Returns a raw response.
     *
     * @param  string $method
     * @param  string $endPoint
     * @return Object
     */
    public function sendRequest($method = 'GET', $endPoint = '')
    {
        $method  = strtolower($method);
        $options = array_flip(['query', 'headers', 'body']);

        foreach ($options as $key => $value) {

            if (isset($this->{$key})) {
                $options[$key] = $this->{$key};
            } else {
                unset($options[$key]);
            }

        }

        $uri = $this->url.$endPoint;

        $request = $this->client->$method($uri, [], $options);

        return $request->send();

    }

    /**
     * Takes the raw response and gives a simple and clean object.
     *
     * @param  object          $raw
     * @param  ResponseAdapter $response
     * @return Response
     */
    public function getResponse($raw, ResponseAdapter $response)
    {
        $body       = $raw->getBody();
        $body       = $body->__toString();
        $headers    = [];
        $rawHeaders = $raw->getHeaders();

        foreach ($rawHeaders as $key => $value) {
            $headers[$key] = (string) $raw->getHeader($key);
        }

        $statusCode = $raw->getStatusCode();

        $response->setBody($body);
        $response->setHeaders($headers);
        $response->setStatusCode($statusCode);

        return $response;

    }

}
