<?php namespace RestGalleries\Interfaces;

use RestGalleries\Auth\AuthAdapter;
use RestGalleries\Http\RequestAdapter;
use RestGalleries\Interfaces\PhotoAdapter;

/**
 * This interface provides methods like CRUD from an MVC model, for enhance the usability.
 * Provides too from cache, authentication to request and user connection methods.
 */
interface GalleryAdapter
{
    public function all();
    public function find($id);
    public function newRequest(RequestAdapter $http = null);
    public function newPhoto(PhotoAdapter $photo = null);
    public function addPlugin($plugin);

}
