<?php namespace RestGalleries\APIs;

use Illuminate\Support\Fluent;
use RestGalleries\Auth\AuthAdapter;
use RestGalleries\Exception\AuthException;
use RestGalleries\Interfaces\UserAdapter;

/**
 * User description.
 */
abstract class ApiUser implements UserAdapter
{
    /**
     * [$auth description]
     *
     * @var object
     */
    protected $auth;

    /**
     * [$checkUrl description]
     *
     * @var string
     */
    protected $checkUrl;

    /**
     * [$urlRequest description]
     *
     * @var string
     */
    protected $urlRequest;

    /**
     * [$urlAuthorize description]
     *
     * @var string
     */
    protected $urlAuthorize;

    /**
     * [$urlAccess description]
     *
     * @var string
     */
    protected $urlAccess;

    public function __construct(AuthAdapter $auth)
    {
        $this->auth = $auth;
    }

    /**
     * [connect description]
     *
     * @param  array  $clientCredentials
     * @return object
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

        return $this->getUser($data);

    }

    /**
     * [verifyCredentials description]
     *
     * @param  array                                  $tokenCredentials
     * @return object
     */
    public function verifyCredentials(array $tokenCredentials)
    {
        $auth = $this->auth;

        $data = $auth::verifyCredentials($tokenCredentials, $this->checkUrl);

        return $this->getUser($data);

    }

    /**
     * [getUser description]
     *
     * @param  object $data
     * @throws RestGalleries\Exception\AuthException;
     * @return object
     */
    private function getUser($data)
    {
        $user = $this->getArrayData($data);

        if (!$user) {
            throw AuthException('The authentication credentials are outdated or are not valid');
        }

        return new Fluent($user);

    }

    /**
     * [getArrayData description]
     *
     * @param  object        $data
     * @return boolean|array
     */
    abstract protected function getArrayData($data);


}
