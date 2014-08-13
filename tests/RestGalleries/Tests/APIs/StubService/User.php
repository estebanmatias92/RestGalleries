<?php namespace RestGalleries\Tests\APIs\StubService;

class User extends \RestGalleries\APIs\ApiUser
{
    protected $checkUrl     = 'http://www.mockservice.com/rest/user';
    protected $urlRequest   = 'http://www.mockservice.com/oauth/request_token';
    protected $urlAuthorize = 'http://www.mockservice.com/oauth/authorize';
    protected $urlAccess    = 'http://www.mockservice.com/oauth/access_token';

    protected function extractUserArray($source)
    {
        if (isset($source->error)) {
            return false;
        }

        $dataUser   = $source->user;
        $dataTokens = $source->tokens;

        $user                           = [];
        $user['id']                     = $dataUser->id;
        $user['realname']               = $dataUser->name .$dataUser->last_name;
        $user['username']               = $dataUser->username;
        $user['url']                    = $dataUser->profile_url;

        $credentials                    = [];
        $credentials['consumer_key']    = $dataTokens->consumer_key;
        $credentials['consumer_secret'] = $dataTokens->consumer_secret;
        $credentials['token']           = $dataTokens->token;
        $credentials['token_secret']    = $dataTokens->token_secret;

        $user['credentials']            = $credentials;

        return $user;

    }

}
