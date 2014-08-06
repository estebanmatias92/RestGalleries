<?php namespace RestGalleries\Http;

use RestGalleries\Http\ResponseAdapter;

/**
 * This class receives the http response from the http client, processes it and returns the data in an orderly manner..
 */
abstract class Response implements ResponseAdapter
{
    /**
     * Stores the body of the Http response.
     *
     * @var string
     */
    protected $body;

    /**
     * Here will be stored the Http response headers array.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Stores status code as integer of the Http response.
     *
     * @var integer
     */
    protected $statusCode;

    /**
     * Initializes the response data process.
     *
     * @param  object $data
     * @return void
     */
    public function __construct($data)
    {
        $this->processResponseData($data);
    }

    /**
     * Process the http client response and separates it into variables.
     *
     * @param  object $raw
     * @return void
     */
    abstract protected function processResponseData($raw);

    /**
     * Returns the xml|json as string, array or object.
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
     * Checks if a string matches some type of specified variable.
     *
     * @param  string $format
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
     * Returns body string (xml or json) without changes.
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
        if (is_xml($this->body)) {
            return xml_decode($this->body, true);
        }

        if (is_json($this->body)) {
            return json_decode($this->body, true);
        }

    }

    /**
     * It return the xml|json as an object.
     *
     * @return array
     */
    protected function bodyObject()
    {
        if (is_xml($this->body)) {
            return xml_decode($this->body);
        }

        if (is_json($this->body)) {
            return json_decode($this->body);
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
