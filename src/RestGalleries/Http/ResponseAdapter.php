<?php namespace RestGalleries\Http;

interface ResponseAdapter
{
    public function __construct($data);
    public function getBody($format = 'object');
    public function getHeaders();
    public function getStatusCode();

}
