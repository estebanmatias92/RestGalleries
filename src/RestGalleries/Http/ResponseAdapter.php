<?php namespace RestGalleries\Http;

interface ResponseAdapter
{
    public function setBody($body);
    public function setHeaders(array $headers);
    public function setStatusCode($statusCode);
    public function getBody($parse = 'json');
    public function getHeaders();
    public function getStatusCode();
}
