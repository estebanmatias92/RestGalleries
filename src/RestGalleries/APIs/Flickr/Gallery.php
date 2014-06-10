<?php namespace RestGalleries\APIs\Flickr;

use RestGalleries\APIs\ApiGallery;
use RestGalleries\Exception\RestGalleriesException;

/**
 * An specific API client for interact with Flickr services.
 * Uses HTTP Client for interact via Restful with the service API.
 */
class Gallery extends ApiGallery
{
    protected $endPoint = 'http://api.flickr.com/services/rest/';

    protected $http = null;

    private $defaultQuery = [
        'format'         => 'json',
        'nojsoncallback' => 1
    ];

    protected function fetchIds()
    {
        $ids   = [];
        $query = array_merge(
            $this->defaultQuery,
            [
                'page'                 => 1,
                'per_page'             => 50,
                'primary_photo_extras' => ''
            ]
        );

        do {
            $this->http->setQuery($query);
            $response = $this->http->GET();
            $body     = $response->getBody('array');

            if ($body['stat'] == 'fail') {
                return;
            }

            $photosets = &$body['photosets'];
            $newIds    = $this->extractIdsArray($photosets);
            $ids       = $this->addIds($ids, $newIds);

        } while (++$query['page'] <= $photosets['pages']);

        return $ids;

    }


    private function extractIdsArray($data)
    {
        return array_pluck($data['photoset'], 'id');
    }

    private function addIds(array $ids, array $newIds)
    {
        if (! empty($newIds)) {
            return array_merge($ids, $newIds);
        }
    }

    protected function fetchGallery($id)
    {
        $query = array_merge(
            $this->defaultQuery,
            [
                'photoset_id' => $id,
            ]
        );

        $this->http->setQuery($query);
        $response = $this->http->GET();
        $body     = $response->getBody();

        return $this->extractGalleryArray($body);

    }

    private function extractGalleryArray($data)
    {
        if ($data->stat == 'fail') {
            return;
        }

        $photoset = &$data->photoset;
        $photo    = &$this->photo;

        $gallery                = [];
        $gallery['id']          = $photoset->id;
        $gallery['title']       = $photoset->title->_content;
        $gallery['description'] = $photoset->description->_content;
        $gallery['photos']      = $photo->all($photoset->id);
        $gallery['created']     = $photoset->date_create;
        $gallery['url']         = 'https://www.flickr.com/photos/';
        $gallery['url']         .= $photoset->owner;
        $gallery['url']         .= '/sets/';
        $gallery['url']         .= $photoset->id;
        $gallery['size']        = $photoset->count_photos;
        $gallery['user_id']     = $photoset->id;
        $gallery['thumbnail']   = $photoset->primary;
        $gallery['views']       = $photoset->count_views;

        return $gallery;

    }


}

