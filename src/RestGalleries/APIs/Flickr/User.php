<?php namespace RestGalleries\APIs\Flickr;

use RestGalleries\APIs\ApiUser;

/**
 * Specific API user to normalize obtained data.
 */
class User extends ApiUser
{
    protected $checkUrl     = 'https://api.flickr.com/services/rest/?method=flickr.auth.oauth.checkToken';
    protected $urlRequest   = 'https://www.flickr.com/services/oauth/request_token';
    protected $urlAuthorize = 'https://www.flickr.com/services/oauth/authorize';
    protected $urlAccess    = 'https://www.flickr.com/services/oauth/access_token';

    protected function getArrayData($data)
    {
        if (stristr($data->err['msg'], 'invalid')) {
            return false;
        }

        $user                    = [];
        $user['id']              = (string) $data->user['nsid'];
        $user['realname']        = (string) $data->user['fullname'];
        $user['username']        = (string) $data->user['username'];
        $user['url']             = 'https://secure.flickr.com/people/'.$user['username'];

        $user['consumer_key']    = (string) $data->tokens['consumer_key'];
        $user['consumer_secret'] = (string) $data->tokens['consumer_secret'];
        $user['token']           = (string) $data->tokens['token'];
        $user['token_secret']    = (string) $data->tokens['token_secret'];

        return $user;

    }

}
