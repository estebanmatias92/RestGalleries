<?php namespace RestGalleries\APIs;

use RestGalleries\Auth\AuthAdapter;
use RestGalleries\Exception\RestGalleriesException;
use RestGalleries\Http\HttpAdapter;
use RestGalleries\Interfaces\UserAdapter;
use RestGalleries\Support\Traits\Overload;

/**
 * User description.
 */
abstract class ApiUser implements UserAdapter
{
    use Overload; // un-useful

    protected $urlCheck;
    protected $urlRequest;
    protected $urlAuthorize;
    protected $urlAccess;

    protected $http;
    protected $auth;

    public $id;
    public $url;
    public $username;
    public $realname;
    public $token;
    public $token_secret;
    public $client_id;
    public $client_secret;

    public function __construct(HttpAdapter $http, AuthAdapter $auth)
    {
        $this->auth = $auth;
        $this->http = $http; // un-useful
    }

    /**
     * [connect description]
     *
     * @param  array  $clientCredentials
     * @return array
     */
    public function connect(array $clientCredentials)
    {
        $endPoints = [
            'request'   => $this->urlRequest,
            'authorize' => $this->urlAuthorize,
            'access'    => $this->urlAccess,
        ];

        $auth = $this->auth;

        $data = $auth::connect($clientCredentials, array_filter($endPoints));

        return $this->getObject($data);

    }

    /**
     * [verifyCredentials description]
     *
     * @param  array                                  $tokenCredentials
     * @return boolean|RestGalleries\APIs\Flickr\User
     */
    public function verifyCredentials(array $tokenCredentials)
    {
        $auth = $this->auth;

        $data = $auth::verifyCredentials($tokenCredentials, $this->urlCheck);

        return $this->getObject($data);

    }

    /**
     * [getObject description]
     *
     * @param  SimpleXmlElement               $data
     * @return RestGalleries\APIs\Flickr\User
     */
    abstract protected function getObject($data);

    protected function setDataTokens(&$data, $tokens)
    {
        foreach ($tokens as $key => $value) {

            $attribute = camel_case($key);

            if (!isset($data->{$attribute})) {
                $data->{$attribute} = $value;
            }

        }

    }

    protected function getTokensFiltered($tokens)
    {
        $keyTokens1 = ['token', 'token_secret'];
        $keyTokens2 = ['acces_token', 'expires'];

        foreach ($tokens as $key => $value) {

            $keyFiltered = str_replace('oauth_', '', $key);

            if (!empty($keyFiltered)) {
                array_forget($tokens, $key);
                array_set($tokens, $keyFiltered, $value);
            }

        }

        if (condition) {
            # code...
        }

        return $tokens;
    }

}
