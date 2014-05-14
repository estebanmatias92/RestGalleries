<?php namespace RestGalleries\APIs;

use RestGalleries\Auth\AuthAdapter;
use RestGalleries\Exception\RestGalleriesException;
use RestGalleries\Interfaces\UserAdapter;

/**
 * User description.
 */
abstract class ApiUser implements UserAdapter
{
    protected $checkUrl;
    protected $urlRequest;
    protected $urlAuthorize;
    protected $urlAccess;

    protected $auth;

    public $id;
    public $url;
    public $username;
    public $realname;
    public $token;
    public $token_secret;
    public $client_id;
    public $client_secret;

    public function __construct(AuthAdapter $auth)
    {
        $this->auth = $auth;
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

        $data = $auth::connect($clientCredentials, array_filter($endPoints), $this->checkUrl);

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

        $data = $auth::verifyCredentials($tokenCredentials, $this->checkUrl);

        return $this->getObject($data);

    }

    /**
     * [getObject description]
     *
     * @param  SimpleXmlElement               $data
     * @return RestGalleries\APIs\Flickr\User
     */
    abstract protected function getObject($data);

}
