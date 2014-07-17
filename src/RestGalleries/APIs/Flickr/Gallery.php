<?php namespace RestGalleries\APIs\Flickr;

use RestGalleries\APIs\ApiGallery;

/**
 * Specific implementation of the GalleryAdapter interface, makes requests and normalizes the given data for return them.
 */
class Gallery extends ApiGallery
{
    protected $endPoint = 'http://api.flickr.com/services/rest/';

    /**
     * Common query values for all the requests.
     *
     * @var array
     */
    private $defaultQuery = [
        'format'         => 'json',
        'nojsoncallback' => 1
    ];

    protected function fetchIds()
    {
        $ids     = [];
        $query   = array_merge(
            $this->defaultQuery,
            [
                'method'               => 'flickr.photosets.getList',
                'page'                 => 1,
                'per_page'             => 50,
                'primary_photo_extras' => ''
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

            $photosets = &$body['photosets'];
            $newIds    = $this->extractIdsArray($photosets);
            $ids       = $this->addIds($ids, $newIds);

        } while (++$query['page'] <= $photosets['pages']);

        return $ids;

    }

    /**
     * Extracts from the given array data, the identifiers of galleries.
     *
     * @param  array $data
     * @return array
     */
    private function extractIdsArray($data)
    {
        return array_pluck($data['photoset'], 'id');
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

    protected function fetchGallery($id)
    {
        $query = array_merge(
            $this->defaultQuery,
            [
                'method'      => 'flickr.photosets.getInfo',
                'photoset_id' => $id,
            ]
        );

        $body = $this->newRequest()
            ->setQuery($query)
            ->GET()
            ->getBody();

        return $this->extractGalleryArray($body);

    }

    /**
     * Extracts the data for the object gallery from the array data given, normalizes them and returns them.
     *
     * @param  object $data
     * @return array|null
     */
    private function extractGalleryArray($data)
    {
        if ($data->stat == 'fail') {
            return;
        }

        $photoset = &$data->photoset;
        $photo    = $this->newPhoto();

        $gallery                = [];
        $gallery['id']          = $photoset->id;
        $gallery['title']       = $photoset->title->_content;
        $gallery['description'] = $photoset->description->_content;
        $gallery['photos']      = $photo->all($photoset->id);
        $gallery['created']     = (string) $photoset->date_create;
        $gallery['url']         = 'https://www.flickr.com/photos/';
        $gallery['url']         .= $photoset->owner;
        $gallery['url']         .= '/sets/';
        $gallery['url']         .= $photoset->id;
        $gallery['size']        = $photoset->count_photos;
        $gallery['user_id']     = $photoset->owner;
        $gallery['thumbnail']   = $photoset->primary;
        $gallery['views']       = $photoset->count_views;

        return $gallery;

    }

}
