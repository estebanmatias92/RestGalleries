<?php namespace RestGalleries\Http;



/**
 * This interface allows normalizing responses of different Http clients to maintain consistency in the returned data.
 */
interface ResponseAdapter
{
    public function __construct($data);
    public function getBody($format = 'object');
    public function getHeaders();
    public function getStatusCode();

}
