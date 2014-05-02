<?php namespace RestGalleries\Interfaces;

use RestGalleries\Interfaces\ApiAccount;
use RestGalleries\Interfaces\ApiGallery;

interface ApiFacade
{
    public function __construct(ApiAccount $Account, ApiGallery $Gallery);
    public function setAccount(array $data);
    public function all();
    public function find($id);
    public function findAccount($id);
    public function connect($callbackUrl);
    public function disconnect();
    public function verifyCredentials();

}
