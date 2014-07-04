<?php namespace RestGalleries\Interfaces;

use RestGalleries\Http\HttpAdapter;

/**
 * This interface provides methods like CRUD from an MVC model, for enhance the usability. Also provides of cache and authentication.
 */
interface PhotoAdapter
{
    public function all($galleryId);
    public function find($id);
    public function newHttp(HttpAdapter $http = null);
    public function setCredentials(array $credentials);
    public function getCredentials();

    public function setCache($fileSystem, array $path);
    public function getCache();

}
