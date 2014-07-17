<?php namespace RestGalleries\Http\Guzzle;

use Guzzle\Http\Client;
use RestGalleries\Http\Request;
use RestGalleries\Http\Plugins\RequestPluginAdapter;

/**
 * Specific http client based on Guzzle Client.
 */
class GuzzleRequest extends Request
{
     /**
     * [$request description]
     *
     * @var \Guzzle\Http\Client
     */
    protected $request;

    public function __construct()
    {
        $this->request = new Client;
    }

    protected function newResponse($data)
    {
        return new GuzzleResponse($data);
    }

    /**
     * [addPlugins description]
     *
     * @param array $plugins
     */
    public function addPlugin(RequestPluginAdapter $plugin)
    {
        $subscriber = $plugin->add();
        $this->request->addSubscriber($subscriber);

        return $this;
    }

    /**
     * Takes the http verb and endpoint (or uri/url) for the request and makes it.
     * Returns a raw response.
     *
     * @param  string $method
     * @param  string $endPoint
     * @return \RestGalleries\Http\Guzzle\GuzzleResponse
     */
    public function sendRequest($method = 'GET', $endPoint = '')
    {
        $options = array_filter([
            'query'   => $this->getQuery(),
            'headers' => $this->getHeaders(),
            'body'    => $this->getBody()
        ]);
        $url = $this->url . $endPoint;

        $request     = $this->request->createRequest($method, $url, $options);
        $rawResponse = $this->request->send($request);

        return $this->newResponse($rawResponse);

    }

}
