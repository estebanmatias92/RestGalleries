<?php namespace RestGalleries\Interfaces;

use RestGalleries\Auth\AuthAdapter;
use RestGalleries\Http\HttpAdapter;
use RestGalleries\Interfaces\PhotoAdapter;

/**
 * GalleryAdapter description.
 */
interface GalleryAdapter
{
    public function __construct(AuthAdapter $auth, HttpAdapter $http, PhotoAdapter $photo);
    public function all();
    public function find($id);
    public function setAuth(array $tokenCredentials);
    public function setCache($fileSystem, array $path);

}
