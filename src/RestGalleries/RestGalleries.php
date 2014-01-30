<?php

namespace RestGalleries;

use RestGalleries\Client;

/**
 * Serves as adapter between the "rest-model", and the gallery APIs facade, has some of the typical methods of an active record.
 */
abstract class RestGalleries
{
    private $api;
    private $client;
    private $args;

    protected $api_key;
    protected $secret_key;

    public $user_id;
    public $galleries;

    public function __construct()
    {
        $this->client = $this->newClient();
    }

    /**
     * Gets the Api to use, and the instance of client facade.
     *
     * @return   object           Returns new client instance.
     */
    private function newClient()
    {
        $this->api = $this->getApi();

        return new ClientApi();
    }

    /**
     * Orders find to the client facade an user by his username.
     *
     * @param    string            $username   User to order find.
     *
     * @return   object/boolean                Returns the user when find him, else returns false.
     */
    public static function findUser($username)
    {
        $instance = new static;

        $api      = $instance->api . "User";

        return $instance->client->findUser(new $api, $instance->api_key, $instance->secret_key, $username);
    }

    /**
     * Orders search to the client facade all existing galleries for a particular user.
     *
     * @param    array            $args   Array of arguments to pass to the API (like, user_id, password, etc).
     *
     * @return   object/boolean           Returns the galleries found, else returns false.
     */
    public static function all($args)
    {
        $instance = new static;

        $api      = $instance->api;

        return $instance->client->get(new $api, $instance->api_key, $instance->secret_key, $args);
    }

    /**
     * Orders search to the client facade a particular gallery for a particular user.
     *
     * @param    array            $args   Array of arguments to pass to the API (like, user_id, password, etc).
     * @param    string/integer   $id     ID gallery number to search.
     *
     * @return   object/boolean           Returns the gallery found, else returns false.
     */
    public static function find($args, $id)
    {
        $instance = new static;

        $api      = $instance->api;

        return $instance->client->find(new $api, $instance->api_key, $instance->secret_key, $args, $id);
    }

    /**
     * Gets the current API to use (from the rest-model name), for send to client facade.
     *
     * @return   string           Returns the API class namespace, ready to be instanced.
     */
    public function getApi()
    {
        if (isset($this->api)) {
            $classname = $this->api;
        } else {
            $classname = get_called_class();
        }

        if (preg_match("@\\\\([\w]+)$@", $classname, $matches)) {
            $classname = $matches[1];
        }

        return __NAMESPACE__ . "\\" . $classname . "\\" . $classname;
    }

}
