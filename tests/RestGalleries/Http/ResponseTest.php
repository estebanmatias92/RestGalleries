<?php

use RestGalleries\Http\Response;

class ResponseTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->url = 'http://api.flickr.com/services/rest/';

        $this->response = new Response;
    }

    public function bodyProvider()
    {
        $json[] = '{"friends": [{"id": 0, "name": "Rhoda Lang"}, {"id": 1, "name": "Heath Brennan"}, {"id": 2, "name": "Steele Christian"} ] }';

        $xml[] = '<?xml version="1.0"?> <catalog> <book id="bk101"> <author>Gambardella, Matthew</author> <title>XML Developer\'s Guide</title> <genre>Computer</genre> <price>44.95</price> <publish_date>2000-10-01</publish_date> <description>An in-depth look at creating applications with XML.</description> </book> </catalog>';

        return [
            $json,
            $xml,
        ];

    }

    /**
     * @dataProvider bodyProvider
     */
    public function testGetBodyReturnsObjects($string)
    {
        $this->response->setBody($string);

        $body = $this->response->getBody();

        $this->assertTrue(is_object($body));

    }

    public function testGetBodyReturnsRawString()
    {
        $this->response->setBody('Just another string for testing');

        $body = $this->response->getBody();

        $this->assertTrue(is_string($body));

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