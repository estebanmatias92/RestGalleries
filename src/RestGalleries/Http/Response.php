<?php namespace RestGalleries\Http;

use RestGalleries\Http\ResponseAdapter;

/**
 * Simple class, sets body, headres and status code from outside, and late returns them.
 */
class Response implements ResponseAdapter
{
    protected $body;
    protected $headers;
    protected $statusCode;

    /**
     * Sets body string or throws an Exception
     *
     * @param string $body
     */
    public function setBody($body)
    {
        if (is_string($body)) {
            $this->body = $body;
        } else {
            throw new \InvalidArgumentException('$body argument should be a string.');
        }
        $this->body = $body;

    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * Sets the status code number or throws an Exception,
     *
     * @param  integer                  $statusCode
     * @throws InvalidArgumentException
     */
    public function setStatusCode($statusCode)
    {
        if (is_integer($statusCode)) {
            $this->statusCode = $statusCode;
        } else {
            throw new \InvalidArgumentException('$statusCode argument should be an integer.');
        }

    }

    /**
     * Returns a json/xml of the body given, or throws an Exception.
     *
     * @param  string                   $parse
     * @throws InvalidArgumentException
     * @return json/xml object
     */
    public function getBody($parse = 'json')
    {
        switch ($parse) {
            case 'json':
                return $this->json($this->body);
                break;

            case 'xml':
                return $this->xml($this->body);
                break;

            default:
                throw new \InvalidArgumentException('Invalid argument value passed for parameter ($parse)');
                break;
        }

    }

    /**
     * Takes an string and converts it into a json object.
     *
     * @param  string      $string
     * @return json object
     */
    protected function json($string)
    {
        return json_decode($string);
    }

    /**
     * Takes an string and converts it into a xml object.
     *
     * @param  string     $string
     * @return xml object
     */
    protected function xml($string)
    {
        return new \SimpleXMLElement($string);
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

}
