<?php namespace RestGalleries\APIs\Flickr;

use RestGalleries\APIs\ApiPhoto;

class Photo extends ApiPhoto
{
    protected $endPoint = 'http://api.flickr.com/services/rest/';

    private $defaultQuery = [
        'format'         => 'json',
        'nojsoncallback' => 1,
    ];

    public function all($galleryId)
    {
        $query = array_merge(
            $this->defaultQuery,
            [
                'method'         => 'flickr.photosets.getPhotos',
                'photoset_id'    => $galleryId,
                'extras'         => 'tags, url_o',
                'privacy_filter' => null,
                'per_page'       => 100,
                'page'           => 1,
                'media'          => 'all',
            ]
        );

        $this->http->setQuery($query);

        $response = $this->http->GET();
        $body     = $response->getBody();


        var_dump($body);

    }

    public function find($id)
    {
        //
    }

}

