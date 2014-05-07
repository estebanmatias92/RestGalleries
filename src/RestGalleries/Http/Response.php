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
     * Returns a json/xml or a raw string of the body given
     *
     * @return json/xml/string
     */
    public function getBody()
    {
        if (is_xml($this->body)) {
            return $this->xml($this->body);
        } elseif (is_json($this->body)) {
            return $this->json($this->body);
        } else {
            return $this->raw();
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
        return simplexml_load_string($string);
    }

    /**
     * Returns body string without changes.
     *
     * @return string
     */
    protected function raw()
    {
        return $this->body;
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
