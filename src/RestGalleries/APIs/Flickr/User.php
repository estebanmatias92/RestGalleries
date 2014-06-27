<?php namespace RestGalleries\APIs\Flickr;

use RestGalleries\APIs\ApiUser;
use RestGalleries\Auth\OhmyAuth\OhmyAuth;

/**
 * Specific API user to normalize obtained data.
 */
class User extends ApiUser
{
    protected $checkUrl     = 'https://api.flickr.com/services/rest/?method=flickr.auth.oauth.checkToken';
    protected $urlRequest   = 'https://www.flickr.com/services/oauth/request_token';
    protected $urlAuthorize = 'https://www.flickr.com/services/oauth/authorize';
    protected $urlAccess    = 'https://www.flickr.com/services/oauth/access_token';

    protected function extractUserArray($data)
    {
        if (stristr($data->err['msg'], 'invalid')) {
            return false;
        }

        $user                    = [];
        $user['id']              = $data->user['nsid'];
        $user['realname']        = $data->user['fullname'];
        $user['username']        = $data->user['username'];
        $user['url']             = 'https://secure.flickr.com/people/';
        $user['url']             .= $user['username'];
        $user['consumer_key']    = $data->tokens['consumer_key'];
        $user['consumer_secret'] = $data->tokens['consumer_secret'];
        $user['token']           = $data->tokens['token'];
        $user['token_secret']    = $data->tokens['token_secret'];

        return $user;

    }

}
