<?php namespace RestGalleries\Http;

use RestGalleries\Http\Plugins\RequestPluginAdapter;

/**
 * Interface to normalize Http clients.
 * Support plugins and several Http methods.
 */
interface RequestAdapter
{
    public static function init($url = '');
    public function addPlugin(RequestPluginAdapter $plugin);
    public function sendRequest($method = 'GET', $endPoint = '');
    public function setBody($body);
    public function setHeaders(array $headers);
    public function setQuery(array $query);

}
