<?php namespace RestGalleries\Http;

use RestGalleries\Http\ResponseAdapter;

/**
 * Interface for Http queries. Provides HTTP verbs (GET, POST, PUT, DELETE), also cache y authentication.
 */
interface RequestAdapter
{
    public static function init($url = '');
    public function addPlugins(array $plugins);
    public function sendRequest($method = 'GET', $endPoint = '');
    public function setBody($body);
    public function setHeaders(array $headers);
    public function setQuery(array $query);


}
