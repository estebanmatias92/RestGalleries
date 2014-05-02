<?php namespace RestGalleries\Interfaces;

/**
 * ApiUser description.
 */
interface ApiUser
{
    public function setAccount(array $data);
    public function find($id);
    public function connect($callbakUrl);
    public function disconnect();
    public function verifyCredentials();
}
