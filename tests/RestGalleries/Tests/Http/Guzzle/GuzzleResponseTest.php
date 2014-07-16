<?php namespace RestGalleries\Tests\Http\Guzzle;

use Mockery;
use RestGalleries\Http\Guzzle\GuzzleResponse;

class GuzzleResponseTest extends \RestGalleries\Tests\TestCase
{
    public function testGetCorrectData()
    {
        $mock = Mockery::mock('Guzzle\\Http\\Client');
        $mock->shouldReceive('getBody')
            ->once()
            ->andReturn($mock);

        $mock->shouldReceive('__toString')
            ->once()
            ->andReturn('Iam a request body.');

        $mock->shouldReceive('getHeaders')
            ->once()
            ->andReturn([
                'User-Agent' => ['I don\'t know.']
            ]);

        $mock->shouldReceive('getStatusCode')
            ->once()
            ->andReturn(200);

        $response   = new GuzzleResponse($mock);
        $body       = $response->getBody('string');
        $headers    = $response->getHeaders();
        $statusCode = $response->getStatusCode();

        assertThat($body, is(stringValue()));
        assertThat($headers, is(arrayValue()));
        assertThat($statusCode, is(integerValue()));

    }

}
