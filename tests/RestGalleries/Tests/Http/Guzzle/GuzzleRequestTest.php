<?php namespace RestGalleries\Tests\Http\Guzzle;

use Mockery;

class GuzzleHttpTest extends \RestGalleries\Tests\TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->url = 'http://www.mockservice.com/rest/';
    }

    public function testAddPluginsCallsAddSubscriber()
    {
        $request = new GuzzleRequestAddPluginsCallsAddSubscriberStub;

        $auth  = Mockery::mock('Guzzle\\Plugin\\Oauth\\OauthPlugin');
        $cache = Mockery::mock('Guzzle\\Plugin\\Cache\\CachePlugin');
        $fakePlugins = [
            'cache' => $cache,
            'auth'  => $auth
        ];

        $request->addPlugins($fakePlugins);

    }

    public function testSendRequestMakesTheRequest()
    {
        $request = new GuzzleRequestSendRequestMakesTheRequestStub;

        $request::init('http://www.mockservice.com/rest/')
            ->setQuery(['dummy-query'])
            ->setHeaders(['dummy-headers'])
            ->setBody('dummy-body')
            ->GET('dummy-endpoint');


    }

}


class GuzzleRequestStub extends \RestGalleries\Http\Guzzle\GuzzleRequest
{
    public function __construct()
    {
        $this->request = Mockery::mock('Guzzle\\Http\\Client');
    }

    protected function newResponse($data)
    {
        $mock = Mockery::mock('RestGalleries\\Http\\Guzzle\\GuzzleResponse', [$data]);

        return $mock;

    }

}

class GuzzleRequestAddPluginsCallsAddSubscriberStub extends GuzzleRequestStub
{
    public function __construct()
    {
        parent::__construct();

        $this->request
            ->shouldReceive('addSubscriber')
            ->times(2);

    }
}

class GuzzleRequestSendRequestMakesTheRequestStub extends GuzzleRequestStub
{
    public function sendRequest($method = 'GET', $endPoint = '')
    {
        $options = [
            'query'   => ['dummy-query'],
            'headers' => ['dummy-headers'],
            'body'    => 'dummy-body'
        ];

        $url = 'http://www.mockservice.com/rest/dummy-endpoint';

        $this->request
            ->shouldReceive('createRequest')
            ->with('GET', $url, $options)
            ->once()
            ->andReturn($this->request);

        $this->request
            ->shouldReceive('send')
            ->with($this->request)
            ->once()
            ->andReturn($this->request);

        return parent::sendRequest($method, $endPoint);

    }

}
