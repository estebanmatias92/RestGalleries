<?php namespace RestGalleries\APIs\Flickr;

use RestGalleries\APIs\ApiPhoto;

/**
 * Specific implementation of the PhotoAdapter interface, makes requests and normalizes the given data for return them.
 */
class Photo extends ApiPhoto
{
    protected $endPoint = 'http://api.flickr.com/services/rest/';

    /**
     * Common query values for all the requests.
     *
     * @var array
     */
    private $defaultQuery = [
        'format'         => 'json',
        'nojsoncallback' => 1,
    ];

    protected function fetchIds($galleryId)
    {
        $ids     = [];
        $query   = array_merge(
            $this->defaultQuery,
            [
                'method'         => 'flickr.photosets.getPhotos',
                'photoset_id'    => $galleryId,
                'extras'         => '',
                'privacy_filter' => 1,
                'per_page'       => 50,
                'page'           => 1,
                'media'          => 'photos',
            ]
        );
        $request = $this->newRequest();

        do {
            $body = $request->setQuery($query)
                ->GET()
                ->getBody('array');

            if ($body['stat'] == 'fail') {
                return;
            }

            $photoset = &$body['photoset'];
            $newIds   = $this->extractIdsArray($photoset);
            $ids      = $this->addIds($ids, $newIds);

        } while (++$query['page'] <= $photoset['pages']);

        return $ids;

    }

    /**
     * Extracts from the given array data, the identifiers of photos.
     *
     * @param  array $data
     * @return array
     */
    private function extractIdsArray($data)
    {
        return array_pluck($data['photo'], 'id');
    }

    /**
     * Adds to the identifiers array, a new array of identifiers.
     *
     * @param  array $ids
     * @param  array $newIds
     * @return array|null
     */
    private function addIds(array $ids, array $newIds)
    {
        if (! empty($newIds)) {
            return array_merge($ids, $newIds);
        }
    }

    protected function fetchPhoto($id)
    {
        $query = array_merge(
            $this->defaultQuery,
            [
                'method'   => 'flickr.photos.getInfo',
                'photo_id' => $id,
                'secret'   => '',
            ]
        );

        $body = $this->newRequest()
            ->setQuery($query)
            ->GET()
            ->getBody();

        return $this->extractPhotoArray($body);

    }

    /**
     * Specific data are stored into an array and are returned, when it receive an failure, returns null.
     *
     * @param  object $data
     * @return array|null
     */
    private function extractPhotoArray($source)
    {
        if ($source->stat == 'fail') {
            return;
        }

        $data  = &$source->photo;

        $photo                     = [];
        $photo['id']               = $data->id;
        $photo['title']            = $data->title->_content;
        $photo['description']      = $data->description->_content;
        $photo['url']              = $data->urls->url[0]->_content;
        $photo['created']          = (string) $data->dates->posted;
        $photo['views']            = $data->views;
        $images                    = $this->fetchImages($photo['id']);
        $photo['source']           = $images[1];
        $photo['source_thumbnail'] = $images[0];

        return $photo;

    }

    /**
     * This function makes the request to get images sources from a photo and returns them as an array.
     *
     * @param  string $photoId
     * @return array
     */
    private function fetchImages($photoId)
    {
        $query = array_merge(
            $this->defaultQuery,
            [
                'method'   => 'flickr.photos.getSizes',
                'photo_id' => $photoId,
            ]
        );

        $body = $this->newRequest()
            ->setQuery($query)
            ->GET()
            ->getBody('array');

        return $this->extractImagesArray($body);

    }

    /**
     * Extracts an array of images from the photo array and returns them.
     *
     * @param  array $source
     * @return array
     */
    private function extractImagesArray($source)
    {
        $sizes  = array_get($source, 'sizes.size');
        $images = array_where($sizes, function ($key, $value) {
            return in_array($value['label'], ['Original', 'Small 320']);
        });

        return array_fetch($images, 'source');

    }

}
