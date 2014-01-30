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

        return $instance->client->findUser(new $api, $instance->getArgs(), $username);
    }

    /**
     * Orders search to the client facade all existing galleries for a particular user.
     *
     * @return   object/boolean           Returns the galleries found, else returns false.
     */
    public static function all()
    {
        $instance = new static;

        $api      = $instance->api;

        return $instance->client->get(new $api, $instance->getArgs());
    }

    /**
     * Orders search to the client facade a particular gallery for a particular user.
     *
     * @param    string/integer   $id   ID gallery number to search.
     *
     * @return   object/boolean         Returns the gallery found, else returns false.
     */
    public static function find($id)
    {
        $instance = new static;

        $api      = $instance->api;

        return $instance->client->find(new $api, $instance->getArgs(), $id);
    }

    /**
     * Sets and returns the arguments to active-record functions.
     * I know, so is wrong :/, should do better.
     *
     * @return   array           An array with the arguments for the API client.
     */
    public function getArgs()
    {
        return [
            'api_key'    => $this->api_key,
            'secret_key' => $this->secret_key,
            'user_id'    => $this->user_id,
        ];
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
            $classname = $classname . "\\" . $matches[1];
        }

        return $classname;
    }

}
