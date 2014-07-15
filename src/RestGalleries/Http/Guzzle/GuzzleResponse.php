<?php namespace RestGalleries\Http\Guzzle;

use RestGalleries\Http\Response;

class GuzzleResponse extends Response
{
    /**
     * Description.
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
