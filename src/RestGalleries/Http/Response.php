<?php namespace RestGalleries\Http;

use RestGalleries\Http\ResponseAdapter;

/**
 * Simple class, sets body, headres and status code from outside, and late returns them.
 */
abstract class Response implements ResponseAdapter
{
    /**
     * [$body description]
     *
     * @var string
     */
    protected $body;

    /**
     * [$headers description]
     *
     * @var array
     */
    protected $headers = [];

    /**
     * [$statusCode description]
     *
     * @var integer
     */
    protected $statusCode;

    public function __construct($data)
    {
        $this->processResponseData($data);
    }

    abstract protected function processResponseData($raw);

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

        $method = 'body' . ucfirst($format);

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
    protected function bodyString()
    {
        return $this->body;
    }

    /**
     * It return the xml|json as an array.
     *
     * @return array
     */
    protected function bodyArray()
    {
        $body = &$this->body;

        if (is_xml($body = &$this->body)) {
            return xml_decode($body, true);
        }

        if (is_json($body)) {
            return json_decode($body, true);
        }

    }

    /**
     * It return the xml|json as an object.
     *
     * @return array
     */
    protected function bodyObject()
    {
        $body = &$this->body;

        if (is_xml($body)) {
            return xml_decode($body);
        }

        if (is_json($body)) {
            return json_decode($body);
        }

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
