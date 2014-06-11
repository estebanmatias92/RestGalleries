<?php namespace RestGalleries\Interfaces;

use RestGalleries\Auth\AuthAdapter;
use RestGalleries\Http\HttpAdapter;
use RestGalleries\Interfaces\PhotoAdapter;

/**
 * This interface provides methods like CRUD from an MVC model, for enhance the usability.
 * Provides too from cache, authentication to request and user connection methods.
 */
interface GalleryAdapter
{
    public function __construct(AuthAdapter $auth, HttpAdapter $http, PhotoAdapter $photo);
    public function all();
    public function find($id);
    public function setAuth(array $tokenCredentials);
    public function setCache($fileSystem, array $path);

}
