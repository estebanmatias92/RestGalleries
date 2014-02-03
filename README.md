RestGalleries
=============

Is an Client API interface for interact with restful services like Flickr between others image web services.

Requirements
------------

* PHP 5.4.0
* (optional) PHPUnit 3.7+ for tests

Installing
----------

The installation is done via composer, so it is necessary to have it installed. Copy and paste the code into your composer.json file.

Docomentation
-------------

###How to use

To use this library, you can create a model of the application, with the name of the API to be used and which extends to the "rest-orm" this way:

```php
<?php

// Fist call the file via namespaces
use RestGalleries\RestGalleries;

// Create the model to interact with flickr, and extend it
class Flickr extends RestGalleries
{
    // These data are necessary to access and interact with the API
    protected $api_key = 'your-flickr-api-key';
    protected $secret_key = 'your-flickr-secret-key';
}

?>
```

That's it! Now you can use this model in any controller:

    <?php

    // Example namespace
    use Model\Flickr;

    class GalleryController
    {
        // Dummy function to get all galleries from flickr
        public function index()
        {
            $args = [
                'user_id' => 'user-id',
            ];

            $galleries = Flickr::all($args);

            return $galleries;
        }
    }

    ?>

###Functions

##### API::findUser($username)

Parameters:
This function receives as parameter a string with the username of an account.

Return:
If it finds the user returns an object with your account data. But returns false.

    <?php

    $user = API::findUser('johndoe84');

    echo $user->id;
    echo $user->url;
    echo $user->realname;

    // Out
    // 548321895
    // www.webservice.com/johndoe84
    // John Doe

    ?>

##### API::all($args)

With this, we can bring all the galleries of a given user service.

Parameters:
This function receives as parameter an array with a key 'user-id' with a given user ID as the value.

Return:
Returns an array of objects where each object is a gallery. If the gallery is not found returning an empty array.

    <?php

    $args = [
        'user_id' => 548321895; // Or as string "548321895"
    ];

    $galleries = API::all($args);

    foreach ($galleries as $gallery) {
        echo $gallery->id;
        echo $gallery->title;
        echo $gallery->description;
        echo $gallery->url;
        echo $gallery->published;
        $gallery->photos; // It is an array of objects, each object contains a picture data.
        echo $gallery->category;
        echo $gallery->keywords;
        echo $gallery->thumbnail;
        echo $gallery->size; // Gallery count of photos
    }

    // [0]
    //
    // 655548798654898
    // My photos
    // My vacation photos! :)
    // www.webservice.com/johndoe84/gallery/655548798654898
    // 02/02/2012 18:57:03
    // null
    // null
    // www.webservice.com/johndoe84/photos/vacation-thumbnail-655548798654898.jpg
    // 120

    // [1]
    //
    // 998584664758855
    // Another album
    // More photos!
    // www.webservice.com/johndoe84/gallery/998584664758855
    // 03/05/2012 02:15:47
    // null
    // null
    // www.webservice.com/johndoe84/photos/vacation-thumbnail-998584664758855.jpg
    // 16

    ?>

##### API::find($args, $id)

Parameters:
This function receives as parameter an array with a key 'user-id' with a given user ID as a value, and a string or integer with the ID of the gallery to find.

Return:
Returns a data object with the gallery if found. But returns false.

    <?php

    $args = [
        'user_id' => 548321895; // Or as string "548321895"
    ];

    $id = 6487;

    $gallery = API::find($args, $id);

    echo $gallery->id;
    echo $gallery->title;
    echo $gallery->description;
    echo $gallery->url;
    echo $gallery->published;
    $gallery->photos; // It is an array of objects, each object contains a picture data.
    echo $gallery->category;
    echo $gallery->keywords;
    echo $gallery->thumbnail;
    echo $gallery->size; // Gallery count of photos

    // Out
    // 655548798654898
    // My photos
    // My vacation photos! :)
    // www.webservice.com/johndoe84/gallery/655548798654898
    // 02/02/2012 18:57:03
    // null
    // null
    // www.webservice.com/johndoe84/photos/vacation-thumbnail.jpg
    // 5

    ?>

Contributing
------------

Of course you can enhance this library by pull request, marking errors, perhaps adding support for other APIs :B, or whatever you see that this needs for improve. Todo contribution will be welcome.


License
-------

Licensed under the MIT License - see the LICENSE file for details
