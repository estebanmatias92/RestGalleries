<?php RestGalleries\Interfaces;

use RestGalleries\Http\HttpAdapter;

/**
 * PhotoAdapter description.
 */
interface PhotoAdapter
{
    public function __construct(HttpAdapter $http);
    public function all($galleryId);
    public function find($id);
    public function setAuth(array $tokenCredentials);
    public function setCache($fileSystem, array $path);

}
