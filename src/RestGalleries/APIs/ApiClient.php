<?php namespace RestGalleries\APIs;


abstract class ApiClient
{
    protected $username;
    protected $userId;
    protected $signature;
    protected $token;
    protected $secretToken;

    protected $apiKey;
    protected $secretKey;

    public function setAccount($account)
    {
        $this->username    = $account['username'];
        $this->userId      = $account['user_id'];
        $this->token       = $account['token'];
        $this->secretToken = $account['secret_token'];
        $this->signature   = $account['signature'];
        $this->apiKey      = $account['api_key'];
        $this->secretKey   = $account['secret_key'];

    }

}
