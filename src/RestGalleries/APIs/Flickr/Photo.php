<?php namespace RestGalleries\APIs\Flickr;

use RestGalleries\APIs\ApiPhoto;

/**
 * Specific implementation of the PhotoAdapter interface, makes requests and normalizes the given data for return them.
 */
class Photo extends ApiPhoto
{
    protected $endPoint = 'http://api.flickr.com/services/rest/';

    private $defaultQuery = [
        'format'         => 'json',
        'nojsoncallback' => 1,
    ];

    public function getPhotoIds($galleryId)
    {
        $page    = 1;
        $perPage = 50;
        $ids     = [];

        do {
            $query = array_merge(
                $this->defaultQuery,
                [
                    'method'         => 'flickr.photosets.getPhotos',
                    'photoset_id'    => $galleryId,
                    'extras'         => '',
                    'privacy_filter' => 1,
                    'per_page'       => $perPage,
                    'page'           => $page,
                    'media'          => 'photos',
                ]
            );

            $this->http->setQuery($query);

            $response = $this->http->GET();
            $body     = $response->getBody('array');

            $newIds = $this->getArrayIds($body);

            if ($newIds == false) {
                return null;
            }

            $photoset = &$body['photoset'];
            $ids      = array_merge($ids, $newIds);

            ++$page;

        } while ($page <= $photoset['pages']);

        return $ids;

    }

    protected function getArrayIds($data)
    {
        if ($data['stat'] == 'fail') {
            return false;
        }

        $photoset = &$data['photoset'];

        return array_pluck($photoset['photo'], 'id');

    }

    protected function getPhoto($id)
    {
        $query = array_merge(
            $this->defaultQuery,
            [
                'method'   => 'flickr.photos.getInfo',
                'photo_id' => $id,
                'secret'   => '',
            ]
        );

        $this->http->setQuery($query);

        $response = $this->http->GET();
        $body     = $response->getBody();

        $photo = $this->getArrayPhoto($body);

        if($photo == false)
        {
            return null;
        }

        return $photo;

    }

    /**
     * Specific data are stored into an array and are returned.
     *
     * @param  object $data
     * @return array
     */
    protected function getArrayPhoto($data)
    {
        if ($data->stat == 'fail') {
            return false;
        }

        $data = &$data->photo;

        $photo                = [];
        $photo['id']          = $data->id;
        $photo['title']       = $data->title->_content;
        $photo['description'] = $data->description->_content;
        $photo['url']         = $data->urls->url[0]->_content;
        $photo['created']     = (integer) $data->dates->posted;
        $photo['views']       = $data->views;

        $query = array_merge(
            $this->defaultQuery,
            [
                'method'   => 'flickr.photos.getSizes',
                'photo_id' => $data->id,
            ]
        );

        $this->http->setQuery($query);

        $response = $this->http->GET();
        $sizes    = $response->getBody();
        $sizes    = &$sizes->sizes->size;

        $images = array_where($sizes, function ($key, $value) {
            return in_array($value->label, ['Original', 'Small 320']);
        });

        $images = array_flatten($images);

        $photo['source']           = $images[1]->source;
        $photo['source_thumbnail'] = $images[0]->source;

        return $photo;

    }

}

