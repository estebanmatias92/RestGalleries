<?php

use RestGalleries\APIs\ApiTemplate;
use RestGalleries\APIs\Flickr\Gallery;
use RestGalleries\APIs\Flickr\User;
use RestGalleries\Interfaces\ApiFacade;
use RestGalleries\Interfaces\ApiGallery;
use RestGalleries\Interfaces\ApiUser;

class Flickr extends ApiTemplate implements ApiFacade
{
    public function __construct(ApiUser $user, ApiGallery $gallery)
    {
        $this->gallery = isset($gallery) ? $gallery : new Gallery;

        $this->user = isset($user) ? $user : new User;
    }
}
