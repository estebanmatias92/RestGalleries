<?php namespace RestGalleries\Tests\APIs\StubService;

class Gallery extends \RestGalleries\APIs\ApiGallery
{
    protected $endPoint = 'http://www.mockservice.com/rest/';

    protected $credentials = [
        'consumer_key'    => 'stub-consumer-key',
        'consumer_secret' => 'stub-consumer-secret',
        'token'           => 'stub-token',
        'token_secret'    => 'stub-token-secret'
    ];

    protected function fetchIds()
    {

    }

    protected function fetchGallery($id)
    {

    }
}
