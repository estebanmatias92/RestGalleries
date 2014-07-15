<?php namespace RestGalleries\Tests\Http;



class ResponseTest extends \RestGalleries\Tests\TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->bodyJson = '{"friends": [{"id": 0, "name": "Rhoda Lang"}, {"id": 1, "name": "Heath Brennan"}, {"id": 2, "name": "Steele Christian"} ] }';

        $this->bodyXml = '<?xml version="1.0"?> <catalog> <book id="bk101"> <author>Gambardella, Matthew</author> <title>XML Developer\'s Guide</title> <genre>Computer</genre> <price>44.95</price> <publish_date>2000-10-01</publish_date> <description>An in-depth look at creating applications with XML.</description> </book> </catalog>';

    }

    public function testGetCorrectData()
    {
        $fakeRequest          = new \stdClass;
        $fakeRequest->body    = 'Iam a request body.';
        $fakeRequest->headers = [
            'Who'        => 'Iam a header.',
            'User-Agent' => 'I don\'t know.',
        ];
        $fakeRequest->statusCode = 200;

        $response   = new ResponseMock($fakeRequest);
        $body       = $response->getBody('string');
        $headers    = $response->getHeaders();
        $statusCode = $response->getStatusCode();

        assertThat($body, is(stringValue()));
        assertThat($headers, is(arrayValue()));
        assertThat($statusCode, is(integerValue()));

    }

    public function testGetBodyJsonArrayFormat()
    {
        $fakeRequest             = new \stdClass;
        $fakeRequest->body       = $this->bodyJson;
        $fakeRequest->headers    = null;
        $fakeRequest->statusCode = null;

        $response   = new ResponseMock($fakeRequest);
        $body       = $response->getBody('array');

        assertThat($body, is(arrayValue()));

    }

    public function testGetBodyJsonObjectFormat()
    {
        $fakeRequest             = new \stdClass;
        $fakeRequest->body       = $this->bodyJson;
        $fakeRequest->headers    = null;
        $fakeRequest->statusCode = null;

        $response   = new ResponseMock($fakeRequest);
        $body       = $response->getBody();

        assertThat($body, is(objectValue()));

    }

    public function testGetBodyXmlArrayFormat()
    {
        $fakeRequest             = new \stdClass;
        $fakeRequest->body       = $this->bodyXml;
        $fakeRequest->headers    = null;
        $fakeRequest->statusCode = null;

        $response   = new ResponseMock($fakeRequest);
        $body       = $response->getBody('array');

        assertThat($body, is(arrayValue()));

    }

    public function testGetBodyXmlObjectFormat()
    {
        $fakeRequest             = new \stdClass;
        $fakeRequest->body       = $this->bodyXml;
        $fakeRequest->headers    = null;
        $fakeRequest->statusCode = null;

        $response   = new ResponseMock($fakeRequest);
        $body       = $response->getBody();

        assertThat($body, is(objectValue()));

    }

}

class ResponseMock extends \RestGalleries\Http\Response
{
    protected function processResponseData($raw)
    {
        $this->body       = $raw->body;
        $this->headers    = $raw->headers;
        $this->statusCode = $raw->statusCode;

    }

}
