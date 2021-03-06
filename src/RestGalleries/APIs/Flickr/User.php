<?php namespace RestGalleries\APIs\Flickr;

use RestGalleries\APIs\ApiUser;

/**
 * Specific API user to normalize obtained data.
 */
class User extends ApiUser
{
    protected $checkUrl     = 'https://api.flickr.com/services/rest/?method=flickr.auth.oauth.checkToken&format=json&nojsoncallback=1';
    protected $urlRequest   = 'https://www.flickr.com/services/oauth/request_token';
    protected $urlAuthorize = 'https://www.flickr.com/services/oauth/authorize';
    protected $urlAccess    = 'https://www.flickr.com/services/oauth/access_token';

    protected function extractUserArray($source)
    {
        if ($source->stat == 'fail') {
            return false;
        }

        $dataUser = $source->oauth->user;

        $user                = [];
        $user['id']          = $dataUser->nsid;
        $user['realname']    = $dataUser->fullname;
        $user['username']    = $dataUser->username;
        $user['url']         = 'https://secure.flickr.com/people/';
        $user['url']         .= $user['username'];
        $user['credentials'] = (array) $source->tokens;

        return $user;

    }

}
