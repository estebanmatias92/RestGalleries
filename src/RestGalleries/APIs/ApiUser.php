<?php namespace RestGalleries\APIs;

use Illuminate\Support\Fluent;
use RestGalleries\Auth\AuthAdapter;
use RestGalleries\Auth\OhmyAuth\OhmyAuth;
use RestGalleries\Exception\AuthException;
use RestGalleries\Interfaces\UserAdapter;

/**
 * Makes the hard work for the user classes of the different APIs.
 */
abstract class ApiUser implements UserAdapter
{
    /**
     * Url where to get the account details..
     *
     * @var string
     */
    protected $checkUrl;

    /**
     * OAuth request endpoint.
     *
     * @var string
     */
    protected $urlRequest;

    /**
     * OAuth authorization endpoint.
     *
     * @var string
     */
    protected $urlAuthorize;

    /**
     * OAuth access endpoint.
     *
     * @var string
     */
    protected $urlAccess;

    /**
     * Makes all the OAuth process to connect the app with the API, only, with the client credentials, the oauth endpoints urls, and an URL to get the user data and token credentials.
     *
     * @param  array $clientCredentials
     * @return \Illuminate\Support\Fluent
     */
    public function connect(array $clientCredentials)
    {
        $endPoints = array_filter([
            'request'   => $this->urlRequest,
            'authorize' => $this->urlAuthorize,
            'access'    => $this->urlAccess,
        ]);

        $auth = $this->newAuth();
        $data = $auth::connect($clientCredentials, $endPoints, $this->checkUrl);

        return $this->getUserOrFail($data);

    }

    /**
     * Checks the token credentials and returns an object with the data (and tokens) from the user account.
     * If for any reason the credentials are invalid, throws an exception.
     *
     * @param  array $tokenCredentials
     * @return \Illuminate\Support\Fluent
     */
    public function verifyCredentials(array $tokenCredentials)
    {
        $auth = $this->newAuth();
        $data = $auth::verifyCredentials($tokenCredentials, $this->checkUrl);

        return $this->getUserOrFail($data);

    }

    /**
     * Object builder to create and use the Auth client.
     * In this case, is set up as default OhmyAuth authentication client.
     *
     * @param  \RestGalleries\Auth\AuthAdapter $auth
     * @return \RestGalleries\Auth\AuthAdapter
     */
    public function newAuth(AuthAdapter $auth = null)
    {
        if (empty($auth)) {
            $auth = new OhmyAuth;
        }

        return $auth;

    }

    /**
     * Gets an array of account data and returns an object. If it not get user data throws an exception.
     *
     * @param  object $data
     * @throws \RestGalleries\Exception\AuthException
     * @return \Illuminate\Support\Fluent
     */
    protected function getUserOrFail($data)
    {
        if (! $user = $this->extractUserArray($data)) {
            throw new AuthException('The credentials are not valid or are obsolete.');
        }

        return new Fluent($user);

    }

    /**
     * Normalize request given data into an array.
     *
     * @param  object $data
     * @return array|boolean
     */
    abstract protected function extractUserArray($data);

}
