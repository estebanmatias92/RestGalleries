<?php namespace RestGalleries\APIs\Flickr;

use RestGalleries\APIs\ApiUser;
use RestGalleries\Auth\OhmyAuth\OhmyAuth;

/**
 * Description here.
 */
class User extends ApiUser
{
    protected $urlCheck     = 'https://api.flickr.com/services/rest/?method=flickr.auth.oauth.checkToken';
    protected $urlRequest   = 'https://www.flickr.com/services/oauth/request_token';
    protected $urlAuthorize = 'https://www.flickr.com/services/oauth/authorize';
    protected $urlAccess    = 'https://www.flickr.com/services/oauth/access_token';

    protected function getObject($data)
    {
        $this->id       = (string) $data->user['nsid'];
        $this->username = (string) $data->user['username'];
        $this->realname = (string) $data->user['fullname'];
        $this->url      = 'https://secure.flickr.com/people/'.$this->username;
        $this->token    = (string) $data->token;

        return $this;
    }

}
