<?php namespace RestGalleries\Http;

use RestGalleries\Http\ResponseAdapter;

/**
 * Interface for Http queries. Provides HTTP verbs (GET, POST, PUT, DELETE), also cache y authentication.
 */
interface HttpAdapter
{
    public function GET($endPoint = '');
    public function DELETE($endPoint = '');
    public function POST($endPoint = '');
    public function PUT($endPoint = '');
    public function getResponse($raw, ResponseAdapter $response);
    public function sendRequest($method = 'GET', $endPoint = '');
    public function setAuth(array $credentials);
    public function setBody($body);
    public function setCache($system, array $path = array());
    public function setHeaders(array $headers);
    public function setQuery(array $query);
    public static function init($url = '');


}
