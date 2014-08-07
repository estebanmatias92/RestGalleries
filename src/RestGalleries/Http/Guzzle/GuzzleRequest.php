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
     * Instance var for the Http client.
     *
     * @var \Guzzle\Http\Client
     */
    protected $request;

    /**
     * Initializes the Http client in an instance var.
     *
     * @return void
     */
    public function __construct()
    {
        $this->request = new Client;
    }

    /**
     * Receives Http client response object with the response data and creates an ResponseAdapter object.
     *
     * @param  \Guzzle\Http\Client $data
     * @return \RestGalleries\Http\Guzzle\GuzzleResponse
     */
    protected function newResponse($data)
    {
        return new GuzzleResponse($data);
    }

    /**
     * Adds normalized plugins to the Http client.
     *
     * @param \RestGalleries\Http\Plugins\RequestPluginAdapter $plugin
     */
    public function addPlugin(RequestPluginAdapter $plugin)
    {
        $subscriber = $plugin->add();
        $this->request->addSubscriber($subscriber);

        return $this;
    }

    /**
     * Gets the request options, creates the request, sends it and returns new response object.
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
