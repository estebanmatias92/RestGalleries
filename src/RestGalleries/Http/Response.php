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
     * It returns the xml|json string in any of these formats.
     *
     * @return string|array|object
     */
    public function getBody($format = 'object')
    {
        switch ($format) {
            case 'string':
                return $this->raw();
                break;

            case 'array':
                return $this->getArray();
                break;

            case 'object':
                return $this->getObject();
                break;

            default:
                return null;
                break;
        }

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

    /**
     * It return the xml|json as an array.
     *
     * @return array
     */
    protected function getArray()
    {
        if (is_xml($this->body)) {
            return $this->xml(true);
        } elseif (is_json($this->body)) {
            return $this->json(true);
        }

    }

    /**
     * It return the xml|json as an object.
     *
     * @return array
     */
    protected function getObject()
    {
        if (is_xml($this->body)) {
            return $this->xml();
        } elseif (is_json($this->body)) {
            return $this->json();
        }

    }

    /**
     * Takes an string and converts it into a json object or array.
     *
     * @param  string       $string
     * @return array|object
     */
    protected function json($array = false)
    {
        return json_decode($this->body, $array);
    }

    /**
     * Takes an string and converts it into a xml object or array.
     *
     * @param  string       $string
     * @return array|object
     */
    protected function xml($array = false)
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
