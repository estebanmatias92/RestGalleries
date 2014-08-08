RestGalleries
=============

[![Latest Stable Version](https://poser.pugx.org/restgalleries/restgalleries/v/stable.png)](https://packagist.org/packages/restgalleries/restgalleries) [![Total Downloads](https://poser.pugx.org/restgalleries/restgalleries/downloads.png)](https://packagist.org/packages/restgalleries/restgalleries) [![Build Status](https://travis-ci.org/estebanmatias92/RestGalleries.png?branch=master)](https://travis-ci.org/estebanmatias92/RestGalleries) [![License](https://poser.pugx.org/restgalleries/restgalleries/license.png)](https://packagist.org/packages/restgalleries/restgalleries)

Is an API Client interface for interact with restful image services like Flickr through CRUD methods.

Requirements
------------

* PHP 5.4.0
* (optional) PHPUnit 3.7+ for tests

Installing
----------

The installation is done via composer, so it is necessary to have it installed. Copy and paste the code into your composer.json file.


    "require": {
        "restgalleries/restgalleries": "0.5.*"
    }


Docomentation
-------------

###How to use

To use this library, you can create a model of the application, with the name of the API to be used:

```php
<?php

// Fist call the file via namespaces
use RestGalleries\RestGallery;

// Create the model to interact with flickr, and extend it
class Flickr extends RestGallery
{
    // These data are necessary to access and interact with the API
    protected $clientCredentials = [
        'consumer_key'    => 'your-flickr-api-key',
        'consumer_secret' => 'your-flickr-secret-key'
        'callback'        => 'http://www.myapp.com/...' // Its optional, you can put it as parameter for 'connect' method
    ];

}

?>
```

That's all! Now you can use this model anywhere:

```php
<?php

// Example namespace
use Model\Flickr;

// If you don't have the user/account tokens, you should connect the model to authenticate and receive the tokens to use them.
$user = Flickr::connect();

$tokenCredentials = [
    'consumer_key'    => $user->consumer_key,
    'consumer_secret' => $user->consumer_secret,
    'token'           => $user->token,
    'token_secret'    => $user->token_secret
];

$model = new Flickr;

// Always needs authentication to start to interact with the api through CRUD methods
$model->setAuth($tokenCredentials)

// And now, you have all galleries from your Flickr account into an array!
$galleries = $model->all();

?>
```

###Functions

##### API::connect($callback)

Receives the url callback for authentication and returns a Fluent object with user account properties and tokens (tokens from protocol oauth1 or oauth2).

```php
<?php

$callback = 'http://www.myapp.com/url_to_return_after_authentication';
$user     = API::connect($callback);

// Account data
$user->id;
$user->realname;
$user->username;
$user->url;
// Account tokens for oauth1
$user->consumer_key;
$user->consumer_secret;
$user->token;
$user->token_secret;
// Account tokens for oauth2
$user->access_token;
$user->expires;

?>
```

##### API::verifyCredentials($tokenCredentials)

Receives an array with the tokens credentials (for protocol oauth1 or oauth2) a Fluent object with the above properties or false in error case.

```php
<?php

$tokenCredentials = [
    'consumer_key'    => 'your-service-api-key',
    'consumer_secret' => 'your-service-api-secret',
    'token'           => 'any-service-account-token',
    'token_secret'    => 'any-service-account-token-secret'
];

$user = API::verifyCredentials($tokenCredentials);

$user->id;
$user->realname;
$user->username;
$user->url;
$user->consumer_key;
$user->consumer_secret;
$user->token;
$user->token_secret;

?>
```

##### API::setAuth($tokenCredentials)

Receives the token credentials (for protocol oauth1 or oauth2) and creates plugin to then make the authentication.
Returns the model instance to make a fluent interface.

```php
<?php

$tokenCredentials = [
    'access_token' => 'any-service-access-token',
    'expires'      => 'any-service-expire-date'
];

$model = new Api;
$model->setAuth($tokenCredentials);

?>
```

##### API::setCache($system, $path)

Receives as string the cache system to use (file or array for now), and as array the path to store the cache files (folder for now).
Returns the model instance to make a fluent interface.

```php
<?php

$system = 'file';
$path   = [
    'forder' => 'C:\Root\...'
];

$model = new Api;
$model->setCache($system, $path);

?>
```

##### API::getPlugins()

Returns an array with the established plugins, every keys has the plugin name, and has the plugin adapter object as value.

```php
<?php

$model   = new Api;
$plugins = $model->setCache($system, $path)
    ->setAuth($tokenCredentials)
    ->getPlugins();

$plugins['cache'];
$plugins['auth'];

?>
```

##### API::all()

Returns a Collection with all the object galleries found.

```php
<?php

$model     = new Api;
$galleries = $model->all();

$galleries[0]->id;
$galleries[0]->title;
$galleries[0]->description;
$galleries[0]->photos; // An array (Collection) of photo object
$galleries[0]->created;
$galleries[0]->url;
$galleries[0]->size;
$galleries[0]->user_id;
$galleries[0]->thumbnail;
$galleries[0]->views;

$photo = $galleries[0]->photos[0];

// Each photo object (Fluent class) contains this properties.
$photo->id;
$photo->title;
$photo->description;
$photo->url;
$photo->created;
$photo->views;
$photo->source;
$photo->source_thumbnail;

?>
```

##### API::find($id)

Receives the gallery id and returns a Fluent object with the gallery data.

```php
<?php

$model   = new Api;
$gallery = $model->find('any-service-gallery-id');

$gallery->id;
$gallery->title;
$gallery->description;
$gallery->photos; // An array (Collection) of photo object
$gallery->created;
$gallery->url;
$gallery->size;
$gallery->user_id;
$gallery->thumbnail;
$gallery->views;

?>
```

Contributing
------------

Of course you can enhance this library through pull request, marking errors, perhaps adding support for other APIs :B, or whatever you see that this needs for improve. Todo contribution will be welcome.


License
-------

Licensed under the MIT License - see the LICENSE file for details
