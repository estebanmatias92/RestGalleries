<?php namespace RestGalleries\Http\Guzzle;

use RestGalleries\Http\Response;

/**
 * Specific http client (as response object) based on Guzzle Client.
 */
class GuzzleResponse extends Response
{
    /**
     * Receives the Http client response, it is processed and stores it into the object properties.
     *
     * @param  \Guzzle\Http\Client $raw
     * @return void
     */
    protected function processResponseData($raw)
    {
        $this->body = $raw->getBody()->__toString();
        $rawHeaders = $raw->getHeaders();

        foreach ($rawHeaders as $key => $value) {
            $this->headers[$key] = implode(', ', $value);
        }

        $this->statusCode = $raw->getStatusCode();

    }

}
