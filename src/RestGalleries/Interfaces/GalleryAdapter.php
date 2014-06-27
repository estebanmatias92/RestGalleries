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
    public function all();
    public function find($id);
    public function newHttp(HttpAdapter $http = null);
    public function newPhoto(PhotoAdapter $photo = null);
    public function setCredentials(array $credentials);
    public function getCredentials();

    public function setCache($fileSystem, array $path);
    public function getCache();

}
