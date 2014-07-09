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
        $this->body = $body;

        return $this;

    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;

        return $this;

    }

    /**
     * Sets the status code number or throws an Exception,
     *
     * @param  integer                  $code
     * @throws InvalidArgumentException
     */
    public function setStatusCode($code)
    {
        $this->statusCode = $code;

        return $this;

    }

    /**
     * It returns the xml|json string in any of these formats.
     *
     * @return string|array|object
     */
    public function getBody($format = 'object')
    {
        if (! $this->isValidBodyFormat($format)) {
            return;
        }

        $method = 'getBody' . ucfirst($format);

        return call_user_func_array([$this, $method], [null]);

    }

    /**
     * Checks if a format are between valid formats.
     *
     * @param  string  $format
     * @return boolean
     */
    protected function isValidBodyFormat($format)
    {
        $formats = ['string', 'array', 'object'];

        if (! in_array($format, $formats)) {
            return false;
        }

        return true;

    }

    /**
     * Returns body string without changes.
     *
     * @return string
     */
    protected function getBodyString()
    {
        return $this->body;
    }

    /**
     * It return the xml|json as an array.
     *
     * @return array
     */
    protected function getBodyArray()
    {
        if (is_xml($this->body)) {
            return $this->bodyXml(true);
        }

        if (is_json($this->body)) {
            return $this->bodyJson(true);
        }

    }

    /**
     * It return the xml|json as an object.
     *
     * @return array
     */
    protected function getBodyObject()
    {
        if (is_xml($this->body)) {
            return $this->bodyxml();
        }

        if (is_json($this->body)) {
            return $this->bodyJson();
        }

    }

    /**
     * Takes an string and converts it into a json object or array.
     *
     * @param  string       $string
     * @return array|object
     */
    protected function bodyJson($array = false)
    {
        return json_decode($this->body, $array);
    }

    /**
     * Takes an string and converts it into a xml object or array.
     *
     * @param  string       $string
     * @return array|object
     */
    protected function bodyXml($array = false)
    {
        return xml_decode($this->body, $array);
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
