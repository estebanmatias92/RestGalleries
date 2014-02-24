<?php

namespace RestGalleries;

use RestGalleries\ClientApi;
use RestGalleries\Exception\RestGalleriesException;

/**
 * Serves as adapter between the "rest-model", and the gallery APIs facade, has some of the typical methods of an active record. Also serves as factory to instance the corrent api client class.
 */
abstract class RestGalleries
{
    protected static $api;
    protected static $apiGallery;
    protected static $apiUser;

    protected static $apiKey;
    protected static $secretKey;

    /**
     * Gets the Api to use, and the instance of client facade.
     *
     * @return   object           Returns new client instance.
     */
    private function newClient()
    {
        static::$api        = $this->getApi();
        static::$apiGallery = static::$api . "Gallery";
        static::$apiUser    = static::$api . "User";

        return new ClientApi();
    }

    /**
     * Orders find to the client facade an user by his username.
     *
     * @param    string            $username   User to order find.
     *
     * @return   object/boolean                Returns the user when find him, but returns false.
     */
    public static function findUser($username)
    {
        $instance  = new static;

        $client    = $instance->newClient();

        $apiObject = new static::$apiUser(static::$apiKey, static::$secretKey);

        return $client->findUser($apiObject, $username);
    }

    /**
     * Orders search to the client facade all existing galleries for a particular user.
     *
     * @param    array            $args   Array of arguments to pass to the API (like, user_id, password, etc).
     *
     * @return   object/boolean           Returns the galleries found, but returns an empty array.
     */
    public static function all($args)
    {
        $instance  = new static;

        $client    = $instance->newClient();

        $apiObject = new static::$apiGallery(static::$apiKey, static::$secretKey);

        return $client->get($apiObject, $args);
    }

    /**
     * Orders search to the client facade a particular gallery for a particular user.
     *
     * @param    array            $args   Array of arguments to pass to the API (like, user_id, password, etc).
     * @param    string/integer   $id     ID gallery number to search.
     *
     * @return   object/boolean           Returns the gallery found, but returns false.
     */
    public static function find($args, $id)
    {
        $instance  = new static;

        $client    = $instance->newClient();

        $apiObject = new static::$apiGallery(static::$apiKey, static::$secretKey);

        return $client->find($apiObject, $args, $id);
    }

    /**
     * Gets the current API to use (from the rest-model name), for send to client facade.
     *
     * @return   string           Returns the API class namespace, ready to be instanced.
     */
    public function getApi()
    {
        if (isset(static::$api)) {
            $classname = static::$api;
        } else {
            $classname = get_called_class();
        }

        $classname = ucfirst($classname);

        if (preg_match("@\\\\([\w]+)$@", $classname, $matches)) {
            $classname = $matches[1];
        }

        return __NAMESPACE__ . "\\APIs\\" . $classname . "\\" . $classname;
    }

    /**
     * Use another function for sets properties dinamically.
     *
     * @param   string           $key     Property name.
     * @param   string           $value   Property value.
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Sets properties to the object.
     *
     * @param   string           $key     Property name.
     * @param   string           $value   Property value.
     */
    public function setAttribute($key, $value)
    {
        static::$$key = $value;
    }
}
