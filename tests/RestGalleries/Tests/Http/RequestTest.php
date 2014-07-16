<?php namespace RestGalleries\Tests\Http;

use Mockery;

class RequestTest extends \RestGalleries\Tests\TestCase
{
    public function testSetBody()
    {
        $request = new RequestStub;
        $request->setBody('Iam a request body.');
        $body    = $request->getBody();

        assertThat($body, is(equalTo('Iam a request body.')));

    }

    public function testSetHeaders()
    {
        $request = new RequestStub;
        $request->setHeaders(['We are the request headers.']);
        $headers = $request->getHeaders();

        assertThat($headers, is(equalTo(['We are the request headers.'])));

    }

    public function testSetQuery()
    {
        $request = new RequestStub;
        $request->setQuery(['We are the request queries.']);
        $query = $request->getQuery();

        assertThat($query, is(equalTo(['We are the request queries.'])));

    }

    public function testAddPlugins()
    {
        $plugins = [
            'auth'  => new \stdClass('non-empty'),
            'cache' => new \stdClass('non-empty')
        ];

        $request = new RequestAddPluginsStub;
        $request::init('http://www.mockservice.com/rest/')
            ->addPlugins($plugins);

    }

    public function testSendRequestReturnsResponse()
    {
        $request  = new RequestSendRequestReturnsResponseStub;
        $response = $request::init('http://www.mockservice.com/rest/')
            ->sendRequest();

        assertThat($response, is(anInstanceOf('RestGalleries\Http\ResponseAdapter')));


    }

    public function testGETCallsSendRequest()
    {
        $request = new RequestHttpMethodsStub;
        $result = $request->GET('dummy-endpoint');

        assertThat($result, is(equalTo('Calling sendRequest with GET and dummy-endpoint')));

    }

    public function testPOSTCallsSendRequest()
    {
        $request = new RequestHttpMethodsStub;
        $result = $request->POST('dummy-endpoint');

        assertThat($result, is(equalTo('Calling sendRequest with POST and dummy-endpoint')));

    }

    public function testPUTCallsSendRequest()
    {
        $request = new RequestHttpMethodsStub;
        $result = $request->PUT('dummy-endpoint');

        assertThat($result, is(equalTo('Calling sendRequest with PUT and dummy-endpoint')));

    }

    public function testDELETECallsSendRequest()
    {
        $request = new RequestHttpMethodsStub;
        $result = $request->DELETE('dummy-endpoint');

        assertThat($result, is(equalTo('Calling sendRequest with DELETE and dummy-endpoint')));

    }

}

class ResponseMock extends \RestGalleries\Http\Response
{
    protected function processResponseData($raw) {}
}

class RequestStub extends \RestGalleries\Http\Request
{
    protected $request;

    public function __construct()
    {
        $mock = Mockery::mock('FakeHttpClient');

        $this->request = $mock;
    }

    protected function newResponse($data)
    {
        return new ResponseMock($data);
    }

    public function addPlugins(array $plugins) {}

    public function sendRequest($method = 'GET', $endPoint = '') {}


}

class RequestAddPluginsStub extends RequestStub
{
    public function addPlugins(array $plugins)
    {
        $this->request
            ->shouldReceive('addPlugin')
            ->with(typeOf('object'))
            ->times(2);

        foreach ($plugins as $plugin) {
            $this->request->addPlugin($plugin);
        }

        return $this;
    }

}

class RequestSendRequestReturnsResponseStub extends RequestStub
{
    public function sendRequest($method = 'GET', $endPoint = '')
    {
        $fakeRawResponse = 'dummy-raw-response-data';

        return $this->newResponse($fakeRawResponse);

    }

}


class RequestHttpMethodsStub extends RequestStub
{
    public function sendRequest($method = 'GET', $endPoint = '')
    {
        $stubResponse = 'Calling sendRequest with ';
        $stubResponse .= $method;
        $stubResponse .= ' and ';
        $stubResponse .= $endPoint;

        return $stubResponse;

    }

}
