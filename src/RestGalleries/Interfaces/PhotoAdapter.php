<?php namespace RestGalleries\Interfaces;

use RestGalleries\Http\RequestAdapter;

/**
 * This interface provides methods like CRUD from an MVC model, to enhance the usability. Also provides of cache and authentication.
 */
interface PhotoAdapter
{
    public function all($galleryId);
    public function find($id);
    public function newRequest(RequestAdapter $http = null);
    public function addPlugin($plugin);

}
