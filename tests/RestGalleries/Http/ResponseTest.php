<?php

use RestGalleries\Http\Response;

class ResponseTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->url = 'http://api.flickr.com/services/rest/';

        $this->stringJson = '[{"friends": [{"id": 0, "name": "Rhoda Lang"}, {"id": 1, "name": "Heath Brennan"}, {"id": 2, "name": "Steele Christian"} ] } ]';

        $this->stringXml = '<?xml version="1.0"?> <catalog> <book id="bk101"> <author>Gambardella, Matthew</author> <title>XML Developer\'s Guide</title> <genre>Computer</genre> <price>44.95</price> <publish_date>2000-10-01</publish_date> <description>An in-depth look at creating applications with XML.</description> </book> </catalog>';

        $this->response = new Response;
    }

    public function testGetBodyJson()
    {
        $this->response->setBody($this->stringJson);

        $body = $this->response->getBody('json')[0];

        $this->assertNotEmpty($body->friends);

    }

    public function testGetBodyXml()
    {
        $this->response->setBody($this->stringXml);

        $body = $this->response->getBody('xml');

        $this->assertNotEmpty($body->book);

    }

    public function testGetBodyInvalidArgument()
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->response->setBody($this->stringJson);

        $this->response->getBody('miss');

    }

    public function testGetHeaders()
    {
        $this->response->setHeaders(['Test' => 'Test Header']);

        $this->assertEquals('Test Header', $this->response->getHeaders()['Test']);

    }

    public function testGetStatusCode()
    {
        $this->response->setStatusCode(200);

        $this->assertInternalType('integer', $this->response->getStatusCode());

    }

}
