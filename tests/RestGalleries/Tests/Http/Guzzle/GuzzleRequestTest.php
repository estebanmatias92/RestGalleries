<?php namespace RestGalleries\Tests\Http\Guzzle;

use Mockery;

class GuzzleHttpTest extends \RestGalleries\Tests\TestCase
{
    public function testAddPluginCallsAddSubscriber()
    {
        $request = new GuzzleRequestAddPluginCallsAddSubscriberStub;

        $mock = Mockery::mock('RestGalleries\\Http\\Plugins\\RequestPluginAdapter');
        $mock->shouldReceive('add')
            ->andReturn(
                Mockery::mock('Symfony\\Component\\EventDispatcher\\EventSubscriberInterface')
            );

        $request->addPlugin($mock);

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

    public function testSendRequestReturnsCorrectObject()
    {
        $request  = new GuzzleRequestSendRequestReturnsCorrectObject;
        $response = $request::init()->GET();

        assertThat($response, is(anInstanceOf('RestGalleries\Http\ResponseAdapter')));

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
        $mock = Mockery::mock('RestGalleries\\Http\\ResponseAdapter');
        $mock->shouldReceive('__construct')
            ->with($data);

        return $mock;

    }

}

class GuzzleRequestAddPluginCallsAddSubscriberStub extends GuzzleRequestStub
{
    public function __construct()
    {
        parent::__construct();

        $this->request
            ->shouldReceive('addSubscriber')
            ->once();

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

class GuzzleRequestSendRequestReturnsCorrectObject extends GuzzleRequestStub
{
    public function sendRequest($method = 'GET', $endPoint = '')
    {
        $this->request
            ->shouldReceive('createRequest')
            ->with('GET', null, null)
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
