<?php namespace RestGalleries\Interfaces;

use RestGalleries\Http\HttpAdapter;

/**
 * This interface provides methods like CRUD from an MVC model, for enhance the usability. Also provides of cache and authentication.
 */
interface PhotoAdapter
{
    public function __construct(HttpAdapter $http);
    public function all($galleryId);
    public function find($id);
    public function setAuth(array $tokenCredentials);
    public function setCache($fileSystem, array $path);

}
